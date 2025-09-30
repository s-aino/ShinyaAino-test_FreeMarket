<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // 最小構成：まずは何もしない
    }

    public function boot(): void
    {
        // Fortify が提供する /login /register の表示を
        // あなたの Blade (resources/views/auth/*.blade.php) に差し替える
        Fortify::loginView(fn () => view('auth.login'));
        Fortify::registerView(fn () => view('auth.register'));
    }
}
