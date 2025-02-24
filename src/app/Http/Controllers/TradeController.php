<?php

namespace App\Http\Controllers;

use App\Http\Requests\MessageRequest;
use App\Models\Item;
use App\Models\TradeMessage;
use App\Models\TradeStatus;
use App\Models\SoldItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\TradeCompletedMail;

class TradeController extends Controller
{
    public function show($item_id)
    {
        $item = Item::with('user')->findOrFail($item_id);
        $soldItem = SoldItem::with(['user', 'user.profile'])->where('item_id', $item_id)->first();

        if (!$soldItem) {
            return redirect()->back()->with('error', 'この商品は取引中ではありません');
        }

        // 購入者または出品者のみアクセス可能
        if (Auth::id() !== $soldItem->user_id && Auth::id() !== $item->user_id) {
            return redirect()->back()->with('error', 'この取引画面にはアクセスできません');
        }

        $tradeStatus = TradeStatus::with(['messages.user.profile'])
            ->where('sold_item_id', $soldItem->item_id)
            ->firstOrFail();

        // 未読メッセージを既読に
        $tradeStatus->messages()
            ->where('user_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // 他の取引を取得
        $otherTrades = TradeStatus::with(['soldItem.item'])
            ->whereHas('soldItem', function($query) {
                $query->where('user_id', Auth::id())
                    ->orWhereHas('item', function($q) {
                        $q->where('user_id', Auth::id());
                    });
            })
            ->where('id', '!=', $tradeStatus->id)
            ->get()
            ->sortByDesc(function($trade) {
                return $trade->getLatestMessageAt();
            });

        return view('trade.show', compact('item', 'tradeStatus', 'otherTrades', 'soldItem'));
    }

    public function store(MessageRequest $request, $trade_status_id)
    {
        $message = new TradeMessage();
        $message->trade_status_id = $trade_status_id;
        $message->user_id = Auth::id();
        $message->message = $request->message;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            // storage/app/public/trade_images に保存
            $path = $request->file('image')->store('trade_images', 'public');
            $message->image_url = $path;
        }

        $message->save();

        // 取引ステータスの更新日時を更新
        $message->tradeStatus->touch();

        return back()->with('success', 'メッセージを送信しました');
    }

    public function update(MessageRequest $request, $message_id)
    {
        $message = TradeMessage::findOrFail($message_id);

        if ($message->user_id !== Auth::id()) {
            return back()->with('error', '編集権限がありません');
        }

        $message->message = $request->message;
        $message->save();

        return back()->with('success', 'メッセージを更新しました');
    }

    public function destroy($message_id)
    {
        $message = TradeMessage::findOrFail($message_id);

        if ($message->user_id !== Auth::id()) {
            return back()->with('error', '削除権限がありません');
        }

        $message->delete();

        return back()->with('success', 'メッセージを削除しました');
    }

    public function complete($trade_status_id)
    {
        $tradeStatus = TradeStatus::findOrFail($trade_status_id);

        // 購入者のみが完了できる
        if (Auth::id() !== $tradeStatus->soldItem->user_id) {
            return response()->json(['error' => '取引を完了する権限がありません'], 403);
        }

        $tradeStatus->update(['is_completed' => true]);

        try {
            // 出品者にメール送信
            $seller = $tradeStatus->soldItem->item->user;
            Mail::to($seller->email)->send(new TradeCompletedMail($tradeStatus));
        } catch (\Exception $e) {
            \Log::error('メール送信エラー: ' . $e->getMessage());
            // メール送信エラーは無視して処理を続行
        }

        return response()->json(['success' => true]);
    }
}
