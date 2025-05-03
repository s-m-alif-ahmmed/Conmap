<?php

namespace App\Http\Controllers\API\Package;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Subscription;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $packages = Package::published()
            ->with('packageOptions') // Include packageOptions relationship
            ->withCount([
                'subscriptions as subscribe_count' => function ($query) {
                    $query->where('stripe_status', 'active'); // Count only active subscriptions
                }
            ])
            ->get();

        if ($packages->isEmpty()) {
            return $this->error("Packages not found", 500);
        }

        // Find the highest subscription count
        $maxSubscription = $packages->max('subscribe_count');

        // If all packages have 0 subscriptions, set popular_status to false
        $allZeroSubscriptions = $maxSubscription === 0;

        // Add popular_status based on the highest subscription count
        $packages = $packages->map(function ($package) use ($maxSubscription, $allZeroSubscriptions) {
            $package->popular_status = !$allZeroSubscriptions && $package->subscribe_count === $maxSubscription;
            return $package;
        });

        return $this->ok('Data Retrieved Successfully!', $packages, 200);
    }

    public function show($id)
    {
        $package = Package::published()->with('packageOptions')->withCount([
            'subscriptions as subscribe_count' => function ($query) {
                $query->where('stripe_status', 'active');
            }
        ])->find($id);

        if (!$package) {
            return $this->error("Package not found", 404);
        }

        // Find the highest subscription count across all packages
        $maxSubscription = Package::published()
            ->withCount(['subscriptions as subscribe_count' => function ($query) {
                $query->where('stripe_status', 'active');
            }])
            ->get()
            ->max('subscribe_count');

        // Determine if this package is the most popular
        $package->popular_status = $package->subscribe_count === $maxSubscription;

        return $this->ok('Data Retrieved Successfully!', [
            'subscribe_count' => $package->subscribe_count,
            'popular_status' => $package->popular_status,
            'package' => $package,
        ], 200);
    }

}
