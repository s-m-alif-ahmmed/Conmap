<?php

namespace App\Http\Controllers\API\Subscription;

use App\Http\Controllers\Controller;
use App\Mail\SubscriptionSuccessMail;
use App\Models\Package;
use App\Models\Subscription;
use App\Models\User;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Stripe;
use Stripe\StripeClient;

class SubscriptionController extends Controller
{
    use ApiResponse;

    public function createPaymentIntent(Request $request)
    {
        try {
            // Set the Stripe secret key from .env
            Stripe::setApiKey(env('STRIPE_SECRET'));

            $request->validate([
               'package_id' => 'required',
//                'payment_method_id' => 'required',
            ]);

            $package = Package::where('id', $request->input('package_id'))->first();

            if (!$package) {
                return $this->error('Package not found',500);
            }

            // Convert price to cents (Stripe requires an integer)
            $price = (int) ($package->price * 100);

            // Create a Payment Intent
            $paymentIntent = PaymentIntent::create([
                'amount' => $price,
                'currency' => 'eur',
                'payment_method_types' => ['card'],
//                'payment_method' => $request->input('payment_method_id'),
//                'confirmation_method' => 'manual',
                'confirmation_method' => 'automatic',
                'confirm' => false,
                'setup_future_usage' => 'off_session',
            ]);

//            $paymentMethod = $paymentIntent->payment_method;

            // Return the required data
            return $this->ok([
                'message' => 'Payment intent created successfully!',
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
//                'payment_method_id' => $paymentMethod,
            ], 200);

        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function subscribe(Request $request)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json(['message' => 'You are not authorized, please login'], 401);
        }

        // Get plan ID and payment method from the request
        $planId = $request->input('package_id');
        $paymentMethodId = $request->input('payment_method_id');

        // Find the plan
        $plan = Package::find($planId);

        if (!$plan) {
            return response()->json(['message' => 'Invalid package'], 400);
        }

        try {
            // Check if user has a Stripe customer
            $user = $request->user();

            // Fetch the active subscription (assuming only one active subscription)
            $existingSubscription = $user->subscriptions()->where('stripe_status', 'active')->first();

            if ($existingSubscription) {
                $existingSubscription->cancelNow();
                $existingSubscription->update(['stripe_status' => 'cancelled']);
            }

            if (!$user->stripe_id) {
                // Create Stripe customer if user doesn't have one
                $user->createAsStripeCustomer();
            }

            $trialPeriodDays = $plan->trial_days ?? 0;

            $subscription = $user->newSubscription(
                $plan->stripe_product_id,
                $plan->stripe_price_id
            );

            // Add trial period if it's defined
            if ($trialPeriodDays > 0) {
                $subscription->trialDays($trialPeriodDays);
            }

            // Create the subscription
            $subscription->create($paymentMethodId);

            // Now retrieve the newly created subscription from the database
            $userSubscription = $user->subscriptions()->latest()->first();

            if ($userSubscription) {
                // Update the subscription with package_id
                $userSubscription->update([
                    'package_id' => $plan->id,
                ]);
            }

            $userEmail = $user->email;
            $userSubscription = $user->subscribed()->where('stripe_status', 'active')->first();

            try {
                Mail::to($userEmail)->send(new SubscriptionSuccessMail($plan, $userSubscription));
            } catch (\Exception $e) {
                \Log::warning("Failed to send subscription email to {$userEmail}: " . $e->getMessage());
            }


            return response()->json(['message' => 'Subscription successful'], 200);

        } catch (IncompletePayment $exception) {
            return response()->json(['message' => 'Payment incomplete, authentication required'], 402);
        } catch (\Exception $exception) {
            // General error handling
            return response()->json(['message' => 'Error processing subscription', 'error' => $exception->getMessage()], 500);
        }
    }

    public function successSubscription()
    {
        return response()->json(['message' => 'Subscription successful'], 200);
    }

    public function cancelSubscription()
    {
        $user = Auth::user();

        // Fetch the active subscription (assuming only one active subscription)
        $subscription = $user->subscriptions()->where('stripe_status', 'active')->first();

        if (!$subscription) {
            return response()->json(['message' => 'No active subscription found'], 400);
        }

        try {
            // Cancel the subscription in Stripe
            $subscription->cancelNow();

            // Optionally update the local subscription status in your database
            $subscription->update(['stripe_status' => 'cancelled']);

            return response()->json(['message' => 'Subscription cancelled'], 200);

        } catch (\Exception $exception) {
            return response()->json(['message' => 'Error cancelling subscription', 'error' => $exception->getMessage()], 500);
        }
    }

    public function getSubscriptionStatus()
    {
        $user = Auth::user();

        // Fetch the active subscription (assuming only one active subscription)
        $subscription = $user->subscribed()->where('stripe_status', 'active')->first();

        if (!$subscription) {
            return $this->error('Invalid subscription', 400);
        }

        // Return the status and end date (if available)
        return $this->success('Subscription get successfully!' , $subscription, 200);
    }

    public function userSubscriptionCheck()
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'You are not authorized'], 401);
        }
        $user = Auth::user();

        $subscription = $user->subscriptions()->where('stripe_status', 'active')->first();

        if (!$subscription) {
            $createdAt = Carbon::parse($user->created_at);
            $trialEnd = $createdAt->addDays(14);
            $now = Carbon::now();

            if ($now->greaterThanOrEqualTo($trialEnd)) {
                $remainingTime = "Trial expired";
                $status = false;
            } else {
                $diff = $now->diff($trialEnd);
                $remainingTime = "{$diff->d} days, {$diff->h} hours, {$diff->i} mins";
                $status = true;
            }

            return $this->success('Trial period details', [
                'created_at' => $user->created_at,
                'trial_end' => $trialEnd,
                'remaining_time' => $remainingTime,
                'trial_status' => $status,
                'package_status' => false,
            ], 200);
        }

        $package = Package::where('stripe_product_id', $subscription->type)->first();

        if (!$package) {
            return $this->error('Package not found!', 400);
        }

        // Subscription start date
        $userSubscriptionStartDate = Carbon::parse($subscription->created_at);

        // Subscription end date based on package duration (assuming duration is in days)
        $subscriptionEndDate = $userSubscriptionStartDate->addDays($package->duration);

        // Calculate remaining time
        $now = Carbon::now();

        if ($now->greaterThanOrEqualTo($subscriptionEndDate)) {
            $remainingTime = "Subscription expired";
            $packageStatus = false;
        } else {
            $diff = $now->diff($subscriptionEndDate);

            // Build formatted remaining time
            $remainingTimeParts = [];
            if ($diff->y > 0) $remainingTimeParts[] = "{$diff->y} year";
            if ($diff->m > 0) $remainingTimeParts[] = "{$diff->m} months";
            if ($diff->d > 0) $remainingTimeParts[] = "{$diff->d} days";
            if ($diff->h > 0) $remainingTimeParts[] = "{$diff->h} hours";
            if ($diff->i > 0) $remainingTimeParts[] = "{$diff->i} mins";

            $remainingTime = implode(', ', $remainingTimeParts);
            $packageStatus = true;
        }

        $data = [
            'user_subscription' => $subscription,
            'package' => $package,
            'remaining_time' => $remainingTime,
            'package_status' => $packageStatus,
        ];

        return $this->success('Subscription get successfully!' , $data, 200);

    }

}
