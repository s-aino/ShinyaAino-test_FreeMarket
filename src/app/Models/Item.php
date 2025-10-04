<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    const STATUS_ACTIVE = 'active';
    const STATUS_SOLD   = 'sold';

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'price',
        'status',
        'image_path'
    ];
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
    public function getImageUrlAttribute(): string
    {
        if (!$this->image_path) {
            return asset('img/noimage.png');
        }
        // http(s) で始まる場合は外部URLをそのまま返す
        if (preg_match('#^https?://#i', $this->image_path)) {
            return $this->image_path;
        }
        // それ以外は /storage 配下を前提（storage:link 済み）
        return asset('storage/' . ltrim($this->image_path, '/'));
    }
    public function getIsSoldAttribute(): bool
    {
        $v = $this->status;
        if (is_null($v)) return false;

        // 数値 or 文字列どちらでも対応
        if (is_numeric($v)) return (int)$v === 1;
        return strtolower((string)$v) === 'sold';
    }
}
