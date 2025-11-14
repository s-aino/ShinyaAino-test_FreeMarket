<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\RegisterResponse;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LoginResponse::class, function () {
            return new class implements LoginResponse {
                public function toResponse($request)
                {
                    return redirect()->intended(Session::pull('url.intended', '/'));
                }
            };
        });
        $this->app->singleton(RegisterResponse::class, function () {
            return new class implements RegisterResponse {
                public function toResponse($request)
                {
                    return redirect()->route('profile.edit');
                }
            };
        });
    }

    public function boot(): void
    {
        Fortify::loginView(fn() => view('auth.login'));
        Fortify::registerView(fn() => view('auth.register'));
        Fortify::createUsersUsing(CreateNewUser::class);

        // ▼ 開発(LOCAL)だけレート制限を無効化
        if (app()->environment('local')) {
            RateLimiter::for('login', function (Request $request) {
                return Limit::none(); // ← 429を出さない
            });
            RateLimiter::for('two-factor', function (Request $request) {
                return Limit::none();
            });
        }
        // Fortify::authenticateThrough(function () {
        //     return [
        //         \Laravel\Fortify\Actions\AttemptToAuthenticate::class,
        //         \Laravel\Fortify\Actions\EnsureLoginIsNotThrottled::class,
        //         function ($request) {
        //             // ログイン後 intended があればそこに飛ばす
        //             return redirect()->intended(Session::pull('url.intended', '/'));
        //         },
        //     ];
        // });
    }
}
