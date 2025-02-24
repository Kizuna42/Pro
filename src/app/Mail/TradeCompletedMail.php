<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\TradeStatus;
use App\Models\Item;
use App\Models\User;

class TradeCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $tradeStatus;
    public $item;
    public $buyer;

    public function __construct(TradeStatus $tradeStatus, Item $item, User $buyer)
    {
        $this->tradeStatus = $tradeStatus;
        $this->item = $item;
        $this->buyer = $buyer;
    }

    public function build()
    {
        return $this->subject('取引が完了しました')
                    ->view('emails.trade-completed');
    }
}
