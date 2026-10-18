<?php

namespace App\Providers;

use Filament\Forms\Components\FileUpload;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Every admin file upload is limited to safe types and 5 MB unless a field sets
        // a lower limit itself. SVG, HTML, PHP and other scriptable files are not
        // allowed. The type is checked on the server, not only in the browser.
        FileUpload::configureUsing(function (FileUpload $upload): void {
            $upload
                ->maxSize(5120)
                ->rule('mimes:jpg,jpeg,png,webp,pdf,mp4,webm');
        });

        // Applies to the admin profile page where the password is changed.
        Password::defaults(fn () => Password::min(12)->mixedCase()->numbers());
    }
}