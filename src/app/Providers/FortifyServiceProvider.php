<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Contracts\LoginResponse;
use App\Actions\Fortify\CreateNewUser;
use App\Providers\RouteServiceProvider;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // 登録完了 → プロフィール編集へ
        $this->app->singleton(RegisterResponse::class, fn() => new class implements RegisterResponse {
            public function toResponse($request)
            {
                return redirect()->route('profile.edit');
            }
        });

        // ログイン完了 → 初回はプロフィール編集、以後は HOME（/mypage）へ
        $this->app->singleton(LoginResponse::class, fn() => new class implements LoginResponse {
            public function toResponse($request)
            {
                $u = $request->user();
                $needs = is_null($u->onboarded_at) || empty($u->postal) || empty($u->address);

                return $needs
                    ? redirect()->route('profile.edit')
                    : redirect()->intended(RouteServiceProvider::HOME);
            }
        });
    }

    public function boot(): void
    {
        // Fortifyの画面を自作Bladeへ
        Fortify::loginView(fn() => view('auth.login'));
        Fortify::registerView(fn() => view('auth.register'));

        // これが無いと "CreatesNewUsers is not instantiable"
        Fortify::createUsersUsing(CreateNewUser::class);
    }
}
