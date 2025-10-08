<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function items()
    {
        return $this->hasMany(Item::class);
    }
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class, 'buyer_id');
    } // 購入履歴

    public function defaultAddress()
    {
        return $this->hasOne(Address::class)->where('is_default', true);
    }
    // コメント等で呼べる: $user->avatar_url
    public function getAvatarUrlAttribute(): string
    {
        if (!empty($this->profile_image_path)) {
            // もし path が "public/..." で保存されていたら取り除いて正規化
            $path = preg_replace('#^public/#', '', $this->profile_image_path);

            // /storage/... の公開URLを返す（php artisan storage:link が前提）
            return Storage::disk('public')->url($path);
        }

        // 未設定時の丸いSVGプレースホルダ
        $initial = urlencode(mb_substr($this->name ?? 'U', 0, 1));
        return 'data:image/svg+xml;utf8,' . rawurlencode(
            '<svg xmlns="http://www.w3.org/2000/svg" width="96" height="96" viewBox="0 0 96 96">
           <circle cx="48" cy="48" r="48" fill="#e6e7e8"/>
           <text x="50%" y="55%" dominant-baseline="middle" text-anchor="middle"
                 font-size="42" fill="#23999">' . $initial . '</text>
         </svg>'
        );
    }
    public function likes()
    {
        return $this->hasMany(\App\Models\Like::class);
    }
}
