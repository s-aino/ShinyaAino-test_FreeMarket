<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'postal',
        'prefecture',
        'city',
        'line1',
        'line2',
        'phone',
        'is_default'
    ];
    protected $casts = ['is_default' => 'boolean'];

    // 仕様に合わせて updated_at を無効化（B案）
    public $timestamps = true;
    const UPDATED_AT = null;  // ← これだけでOK

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
