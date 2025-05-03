<?php

namespace App\Filament\Widgets;

use App\Models\Package;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class DashboardOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return auth()->user()->role === 'Super Admin' ? [
            Stat::make(
                'Total Users',
                // Get the total number of users
                User::where('role', 'User')->count()
            )
                ->description('New Users (' . Carbon::now()->format('F') . '): ' . User::where('role', 'User')->whereMonth('created_at', Carbon::now()->month)->count())
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart(
                // Chart data for this month, with the count for each day in the current month
                    collect(range(0, Carbon::now()->daysInMonth - 1))
                        ->map(
                            fn($day) =>
                            User::where('role', 'User')
                                ->whereDate('created_at', Carbon::now()->startOfMonth()->addDays($day))
                                ->count()
                        )
                        ->toArray()
                )
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make(
                'Total Admins',
                // Get the total number of admin users
                User::where('role', 'Admin')->count()
            )
                ->description('New Admins (' . Carbon::now()->format('F') . '): ' . User::where('role', 'Admin')->whereMonth('created_at', Carbon::now()->month)->count())
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart(
                // Chart data for this month, with the count for each day in the current month
                    collect(range(0, Carbon::now()->daysInMonth - 1))
                        ->map(
                            fn($day) =>
                            User::where('role', 'Admin')->whereDate('created_at', Carbon::now()->startOfMonth()->addDays($day))->count()
                        )->toArray()
                )
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make(
                'Total Subscribed Users',
                // Get the total number of subscribed users
                User::whereHas('subscriptions')->count()
            )
                ->description('New Subscribed Users (' . Carbon::now()->format('F') . '): ' . User::whereHas('subscriptions')->whereMonth('created_at', Carbon::now()->month)->count())
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart(
                // Chart data for this month, with the count for each day in the current month
                    collect(range(0, Carbon::now()->daysInMonth - 1))
                        ->map(
                            fn($day) =>
                            User::whereHas('subscriptions')->whereDate('created_at', Carbon::now()->startOfMonth()->addDays($day))->count()
                        )->toArray()
                )
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make(
                'Total Packages',
                // Get the total number of packages
                Package::count()
            )
                ->description('New Packages (' . Carbon::now()->format('F') . '): ' . Package::whereMonth('created_at', Carbon::now()->month)->count())
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart(
                // Chart data for this month, with the count for each day in the current month
                    collect(range(0, Carbon::now()->daysInMonth - 1))
                        ->map(
                            fn($day) =>
                            Package::whereDate('created_at', Carbon::now()->startOfMonth()->addDays($day))->count()
                        )->toArray()
                )
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make(
                'Total Projects',
                // Get the total number of projects
                Project::count()
            )
                ->description('New Projects (' . Carbon::now()->format('F') . '): ' . Project::whereMonth('created_at', Carbon::now()->month)->count())
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart(
                // Chart data for this month, with the count for each day in the current month
                    collect(range(0, Carbon::now()->daysInMonth - 1))
                        ->map(
                            fn($day) =>
                            Project::whereDate('created_at', Carbon::now()->startOfMonth()->addDays($day))->count()
                        )->toArray()
                )
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
        ]: [];
    }
}
