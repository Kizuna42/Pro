<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $fillable = [
        'trade_status_id',
        'rating_user_id',  // 評価をする人（購入者）
        'rated_user_id',   // 評価される人（出品者）
        'rating'           // 評価値（1-5）
    ];

    public function ratingUser()
    {
        return $this->belongsTo(User::class, 'rating_user_id');
    }

    public function ratedUser()
    {
        return $this->belongsTo(User::class, 'rated_user_id');
    }

    public function tradeStatus()
    {
        return $this->belongsTo(TradeStatus::class);
    }
}
