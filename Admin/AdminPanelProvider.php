<?php

declare(strict_types=1);

namespace Admin;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Admin\Models\Flashcard;
use Filament\PanelProvider;
use Admin\Models\FlashcardDeck;
use Filament\Support\Colors\Color;
use Admin\Policies\FlashcardPolicy;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Gate;
use App\Http\Middleware\TrustProxies;
use Admin\Policies\FlashcardDeckPolicy;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: base_path('../Admin/Resources'), for: 'Admin\Resources')
            ->discoverPages(in: base_path('../Admin/Pages'), for: 'Admin\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: base_path('../Admin/Pages'), for: 'Admin\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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
                TrustProxies::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])->authGuard('admin');
    }

    public function boot(): void
    {
        Gate::policy(Flashcard::class, FlashcardPolicy::class);
        Gate::policy(FlashcardDeck::class, FlashcardDeckPolicy::class);
    }
}
