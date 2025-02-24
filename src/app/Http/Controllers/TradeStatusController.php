<?php

namespace App\Http\Controllers;

use App\Mail\TradeCompletedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Models\TradeStatus;
use Illuminate\Http\Request;

class TradeStatusController extends Controller
{
    public function complete(TradeStatus $tradeStatus)
    {
        try {
            DB::transaction(function () use ($tradeStatus) {
                $tradeStatus->update(['is_completed' => true]);

                // メール送信
                $seller = $tradeStatus->soldItem->item->user;
                Mail::to($seller->email)->send(new TradeCompletedMail(
                    $tradeStatus,
                    $tradeStatus->soldItem->item,
                    auth()->user()
                ));
            });

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'エラーが発生しました'
            ], 500);
        }
    }
}
