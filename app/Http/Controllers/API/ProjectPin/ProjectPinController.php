<?php

namespace App\Http\Controllers\API\ProjectPin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Project;
use App\Models\ProjectPin;
use App\Models\Subscription;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ProjectPinController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $per_page = $request->input('per_page');
        $user = auth()->user();
        $data = ProjectPin::where('user_id', $user->id)->with('project');

        if ($per_page) {
            $data = $data->paginate($per_page);
        }else{
            $data = $data->get();
        }

        if ($data) {
            return $this->ok('Data Retrieve Successfully!', $data, 200);
        }
        return $this->error("Credits not found", 500);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
        ]);

        $project = Project::find($validated['project_id']);

        if (!$project) {
            return $this->error("Project not found", 404);
        }

        $subscribedUser = $user->subscriptions()->where('stripe_status', 'active')->first();

        if ($subscribedUser) {
            $package = Package::where('interval', $subscribedUser->package_id)->first();

            $packageUser = Subscription::where('user_id', $user->id)
                ->where('package_id', $subscribedUser->package_id)
                ->where('stripe_status', 'active')
                ->first();
        }

        $userExistPin = ProjectPin::where('user_id', $user->id)->where('project_id', $project->id)->first();

        if ($userExistPin) {
            return $this->error("You already pined this project!", 500);
        }

        if (!$subscribedUser) {
            return $this->handleTrialPinLimit($user, $validated['project_id']);
        }elseif ($packageUser) {
            if ($package->interval == 'month') {
                return $this->handleMonthlyPinLimit($user, $validated['project_id']);
            }elseif ($package->interval == 'year') {
                return $this->handleYearlyPinLimit($user, $validated['project_id']);
            }elseif ($package->interval == 'trail') {
                return $this->handleTrialPinLimit($user, $validated['project_id']);
            }
        }

        return $this->error('Something error found!', 500);
    }

    private function handleMonthlyPinLimit($user, $projectId)
    {
        // Get the subscription start date (use created_at or subscription start date from your logic)
        $subscriptionStartDate = $user->subscriptions()->where('stripe_status', 'active')->first()->created_at;

        $currentMonth = now()->startOfMonth();

        // Get the first day of the month of the subscription start date
        $subscriptionStartMonth = $subscriptionStartDate->copy()->startOfMonth();

        // Check if the current month is before the subscription start month
        if ($currentMonth->lt($subscriptionStartMonth)) {
            $currentMonth = $subscriptionStartMonth;
        }

        // Calculate the total number of months since the subscription started
        $monthsSinceSubscription = $subscriptionStartDate->diffInMonths(now());

        // Max pin limit over 3 months is 150
        $pinsThisMonth = ProjectPin::where('user_id', $user->id)
            ->where('created_at', '>=', $currentMonth)
            ->where('created_at', '<', $currentMonth->copy()->addMonth())
            ->count();

        // Count total pins over the last 3 months
        $totalPinsLast3Months = ProjectPin::where('user_id', $user->id)
            ->where('created_at', '>=', $subscriptionStartDate->copy()->subMonths(3))
            ->count();

        // If total pins in the last 3 months exceeds 150, do not allow pin creation
        if ($totalPinsLast3Months >= 150) {
            return $this->error("Pin create limit exceeded for the last 3 months!", 500);
        }

        // Check if the monthly limit (50 pins per month) is reached
        if ($pinsThisMonth >= 50) {
            return $this->error("Monthly pin limit exceeded (50 pins max per month)!", 500);
        }

        // Create the pin
        $project_pin = ProjectPin::create([
            'user_id' => $user->id,
            'project_id' => $projectId,
        ]);

        return $this->success('Pin created successfully!', $project_pin, 200);
    }

    /**
     * Handle Yearly Pin Limit Logic
     */
    private function handleYearlyPinLimit($user, $projectId)
    {
        $project_pin = ProjectPin::create([
            'user_id' => $user->id,
            'project_id' => $projectId,
        ]);
        return $this->success('Pin created successfully!', $project_pin, 200);
    }

    /**
     * Handle Trial Pin Limit Logic
     */
    private function handleTrialPinLimit($user, $projectId)
    {
        $isNewUser = $user->created_at->diffInDays(now()) <= 14;

        if (!$isNewUser) {
            return $this->error("Project pin not available", 500);
        }

        // Count the number of pinned projects
        $pins = $user->projectPins()->count();

        if ($pins >= 10) {
            return $this->error("Project pin create limit finished!", 500);
        }

        $project_pin = ProjectPin::create([
            'user_id' => $user->id,
            'project_id' => $projectId,
        ]);

        return $this->success('Pin created successfully!', $project_pin, 200);
    }


    public function show($id)
    {
        $user = auth()->user();
        $data = ProjectPin::where('user_id', $user->id)->with('project')->find($id);
        if ($data) {
            return $this->ok('Data Retrieve Successfully!',$data, 200);
        }
        return $this->error("Pin not found", 500);
    }

    public function destroy($id)
    {
        $user = auth()->user();

        $pin = ProjectPin::where('user_id', $user->id)->find($id);

        if (!$pin) {
            return $this->error("Pin not found", 500);
        }

        $pin->delete();

        return $this->ok('Pin deleted successfully!', 200);
    }
}
