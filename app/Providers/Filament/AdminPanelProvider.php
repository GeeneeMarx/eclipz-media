<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\InquiryStatsOverview;
use App\Filament\Widgets\QuickActionsWidget;
use App\Filament\Widgets\RecentInquiriesWidget;
use App\Http\Middleware\EnsureAdminIsActive;
use App\Http\Middleware\SecurityHeaders;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile(isSimple: false)
            ->colors([
                'primary' => Color::hex('#007BFF'),
            ])
            ->favicon(fn () => asset('images/eclipz-icon.svg'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->widgets([
                InquiryStatsOverview::class,
                QuickActionsWidget::class,
                RecentInquiriesWidget::class,
            ])
            ->middleware([
                SecurityHeaders::class,
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
                EnsureAdminIsActive::class,
            ])
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn () => new HtmlString(<<<'HTML'
                    <script>
                        // Copy-to-clipboard for admin "Copy" buttons (project URLs, Inquiry IDs).
                        // Plain vanilla JS - deliberately not a Filament Action/Alpine binding, since
                        // that path proved unreliable inside the table's actions dropdown.
                        window.eclipzCopyProjectUrl = function (button, url) {
                            const label = button.querySelector('[data-copy-label]');
                            const original = label ? label.textContent : null;

                            function showFeedback(text, isError) {
                                if (!label) return;
                                label.textContent = text;
                                if (isError) {
                                    button.classList.add('text-red-500');
                                } else {
                                    button.classList.add('text-green-600');
                                }
                                setTimeout(() => {
                                    label.textContent = original;
                                    button.classList.remove('text-red-500', 'text-green-600');
                                }, 1600);
                            }

                            function fallbackCopy() {
                                try {
                                    const textarea = document.createElement('textarea');
                                    textarea.value = url;
                                    textarea.style.position = 'fixed';
                                    textarea.style.opacity = '0';
                                    document.body.appendChild(textarea);
                                    textarea.focus();
                                    textarea.select();
                                    const successful = document.execCommand('copy');
                                    document.body.removeChild(textarea);
                                    successful ? showFeedback('Copied!', false) : showFeedback('Copy failed', true);
                                } catch (e) {
                                    showFeedback('Copy failed', true);
                                }
                            }

                            if (window.isSecureContext && navigator.clipboard && navigator.clipboard.writeText) {
                                navigator.clipboard.writeText(url)
                                    .then(() => showFeedback('Copied!', false))
                                    .catch(fallbackCopy);
                            } else {
                                fallbackCopy();
                            }
                        };
                    </script>
                HTML),
            );
    }
}