<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoldItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'item_id';

    public $incrementing = false;

    protected $fillable = [
        'item_id',
        'user_id',
        'sending_postcode',
        'sending_address',
        'sending_building'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tradeStatus()
    {
        return $this->hasOne(TradeStatus::class, 'sold_item_id', 'item_id');
    }
}
