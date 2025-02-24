<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TradeMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'trade_status_id',
        'user_id',
        'message',
        'image_url',
        'is_read'
    ];

    protected $casts = [
        'is_read' => 'boolean'
    ];

    public function tradeStatus()
    {
        return $this->belongsTo(TradeStatus::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // メッセージを既読にするメソッド
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }
}
