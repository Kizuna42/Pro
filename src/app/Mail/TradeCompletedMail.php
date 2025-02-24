<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use App\Models\TradeStatus;

class TradeCompletedMail extends Mailable
{
    public $tradeStatus;

    public function __construct(TradeStatus $tradeStatus)
    {
        $this->tradeStatus = $tradeStatus;
    }

    public function build()
    {
        return $this->markdown('emails.trade-completed')
            ->subject('取引が完了しました');
    }
}
