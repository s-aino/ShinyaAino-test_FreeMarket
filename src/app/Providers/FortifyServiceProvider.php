<?php

namespace App\Providers;


use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Contracts\LoginResponse;
use App\Actions\Fortify\CreateNewUser;
use App\Providers\RouteServiceProvider;

use Laravel\Fortify\Actions\AttemptToAuthenticate;
use Laravel\Fortify\Actions\EnsureLoginIsNotThrottled;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;
use App\Actions\Fortify\ValidateLogin;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {

        $this->app->singleton(RegisterResponse::class, function () {
            return new class implements RegisterResponse {
                public function toResponse($request)
                {
                    // 直前に保護URLがあればそこへ、なければ /home へ
                    return redirect()->route('profile.edit'); 
                }
            };
        });
    }
    public function boot(): void
    {
        // Fortifyの画面を自作Bladeへ
        Fortify::loginView(fn() => view('auth.login'));
        Fortify::registerView(fn() => view('auth.register'));

        // これが無いと "CreatesNewUsers is not instantiable"
        Fortify::createUsersUsing(CreateNewUser::class);

        Fortify::authenticateThrough(function ($request) {
            return [
                ValidateLogin::class,
                EnsureLoginIsNotThrottled::class,
                AttemptToAuthenticate::class,
                PrepareAuthenticatedSession::class,
            ];
        });

        // 開発環境だけログイン制限を解除（or 大幅緩和）

        if (app()->environment('local')) {
            RateLimiter::for('login', fn(Request $r) => Limit::none());
            RateLimiter::for('two-factor', fn(Request $r) => Limit::none());
        }
    }
}
