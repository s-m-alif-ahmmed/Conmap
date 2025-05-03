<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Profile;
use App\Filament\Widgets\DashboardOverview;
use App\Filament\Widgets\ProjectsChart;
use App\Filament\Widgets\UsersChart;
use App\Models\SystemSetting;
use App\Models\User;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        try {
            $system = SystemSetting::first();
        }catch (\Exception $error){
            $system = null;
        }

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->loginRouteSlug('login')
            ->brandLogoHeight('3rem')
            ->brandLogo(asset($system?->logo ?? '/frontend/logo.png'))
            ->favicon(asset($system?->favicon ?? '/frontend/favicon.png'))
            ->login()
            ->passwordReset()
            ->emailVerification()
            ->userMenuItems([
                MenuItem::make()
                    ->label('Edit Profile')
                    ->url(fn (): string => Profile::getUrl())
                    ->icon('heroicon-o-cog-6-tooth'),
            ])
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
//                DashboardOverview::class,
                UsersChart::class,
                ProjectsChart::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

}
