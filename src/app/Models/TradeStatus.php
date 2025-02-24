<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'sold_item_id',
        'is_completed'
    ];

    public function soldItem()
    {
        return $this->belongsTo(SoldItem::class, 'sold_item_id', 'item_id');
    }

    public function messages()
    {
        return $this->hasMany(TradeMessage::class);
    }

    public function getLatestMessageAt()
    {
        $latestMessage = $this->messages()->latest()->first();
        return $latestMessage ? $latestMessage->created_at : $this->created_at;
    }

    public function getUnreadMessageCount()
    {
        return $this->messages()
            ->where('user_id', '!=', auth()->id())
            ->where('is_read', false)
            ->count();
    }
}
