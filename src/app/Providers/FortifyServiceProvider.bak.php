<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Contracts\LoginResponse;

use App\Actions\Fortify\CreateNewUser;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Http\Requests\LoginRequest;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // 登録完了 → プロフィール編集へ
        $this->app->singleton(RegisterResponse::class, fn() => new class implements RegisterResponse {
            public function toResponse($request)
            {
                return redirect('/profile/edit'); // ルート名に依存させたくないのでパス指定
            }
        });

        // ログイン完了 → 初回はプロフィール編集、以後は /mypage
        $this->app->singleton(LoginResponse::class, fn() => new class implements LoginResponse {
            public function toResponse($request)
            {
                $u = $request->user();
                // カラムが未作成でも getAttribute なら安全に null を返す
                $onboarded = $u->getAttribute('onboarded_at');
                $postal    = $u->getAttribute('postal');
                $address   = $u->getAttribute('address');

                $needsOnboarding = is_null($onboarded) || empty($postal) || empty($address);

                return $needsOnboarding
                    ? redirect('/profile/edit')
                    : redirect()->intended('/mypage');
            }
        });
    }

    public function boot(): void
    {
        // Fortify の画面を自作 Blade に差し替え
        Fortify::loginView(fn() => view('auth.login'));
        Fortify::registerView(fn() => view('auth.register'));

        // CreateNewUser を Fortify に紐付け（これが無いと "CreatesNewUsers is not instantiable"）
        Fortify::createUsersUsing(CreateNewUser::class);

        // ★LoginRequest の messages()/attributes() を使って事前検証したい場合（推奨）
        Fortify::authenticateUsing(function (Request $request) {
            $req = new LoginRequest();
            Validator::make(
                $request->only('email', 'password'),
                $req->rules(),
                $req->messages(),
                $req->attributes()
            )->validate();

            $user = User::where('email', $request->email)->first();
            return ($user && Hash::check($request->password, $user->password)) ? $user : null;
        });
    }
}
