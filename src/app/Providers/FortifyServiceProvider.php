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

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // ログイン後：直前の intended があればそこへ、なければ '/'
        $this->app->singleton(LoginResponse::class, function () {
            return new class implements LoginResponse {
                public function toResponse($request)
                {
                    return redirect()->intended('/'); // ← ここが最終遷移先
                }
            };
        });

        // 新規登録後：今回は '/' に統一（/profile/edit にしたければここを変更）
        $this->app->singleton(RegisterResponse::class, function () {
            return new class implements RegisterResponse {
                public function toResponse($request)
                {
                    return redirect()->route('profile.edit'); // 例: return redirect('/profile/edit');
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
    }
}
