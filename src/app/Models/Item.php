<?php

namespace App\Models;

use App\Models\User;
use App\Models\Like;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    // ※DBが 'available' を使っているなら、ここを 'available' に変更してください
    public const STATUS_ACTIVE = 'active';
    public const STATUS_SOLD   = 'sold';

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'price',
        'status',
        'image_path'
    ];

    // BladeやJSONで補助属性を常に使いたいので追加
    protected $appends = ['image_url', 'is_sold'];

    public function seller()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function order()
    {
        return $this->hasOne(Order::class);
    }


    // 画像URL（image_path運用）
    public function getImageUrlAttribute(): string
    {
        if (!$this->image_path) {
            return asset('img/noimage.png');
        }
        if (preg_match('#^https?://#i', $this->image_path)) {
            return $this->image_path;
        }
        return asset('storage/' . ltrim($this->image_path, '/'));
    }

    // SOLD判定（文字列/数値どちらでも耐性あり）
    public function getIsSoldAttribute(): bool
    {
        $v = $this->status;
        if (is_null($v)) return false;
        if (is_numeric($v)) return (int)$v === 1;
        return strtolower((string)$v) === self::STATUS_SOLD;
    }


    // いいね（likes テーブル行）への hasMany
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    // この商品をいいねしたユーザー一覧（中間 likes 経由）
    public function likedUsers()
    {
        return $this->belongsToMany(User::class, 'likes')->withTimestamps();
    }

    // 「この商品を $user がいいね済みか？」判定
    public function isLikedBy(?User $user): bool
    {
        if (!$user) return false;
        return $this->likes()->where('user_id', $user->id)->exists();
    }
}
