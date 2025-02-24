<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;
use App\Models\User;
use App\Models\Item;
use App\Models\SoldItem;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Storage;
use App\Models\TradeMessage;
use App\Models\TradeStatus;

class UserController extends Controller
{
    public function profile(){

        $profile = Profile::where('user_id', Auth::id())->first();

        return view('profile',compact('profile'));
    }

    public function updateProfile(ProfileRequest $request){

        $img = $request->file('img_url');
        if (isset($img)){
            $img_url = Storage::disk('local')->put('public/img', $img);
        }else{
            $img_url = '';
        }

        $profile = Profile::where('user_id', Auth::id())->first();
        if ($profile){
            $profile->update([
                'user_id' => Auth::id(),
                'img_url' => $img_url,
                'postcode' => $request->postcode,
                'address' => $request->address,
                'building' => $request->building
            ]);
        }else{
            Profile::create([
                'user_id' => Auth::id(),
                'img_url' => $img_url,
                'postcode' => $request->postcode,
                'address' => $request->address,
                'building' => $request->building
            ]);
        }

        User::find(Auth::id())->update([
            'name' => $request->name
        ]);

        return redirect('/');
    }

    public function mypage(Request $request)
    {
        $user = User::find(Auth::id());
        $page = $request->query('page', 'sell');
        $itemsWithUnreadCounts = collect();

        switch($page) {
            case 'trading':
                // 完了していない取引のみ取得
                $tradeStatuses = TradeStatus::where('is_completed', false)
                    ->whereHas('soldItem', function($query) {
                        $query->where(function($q) {
                            // 自分が購入者の場合
                            $q->where('user_id', Auth::id())
                                // または自分が出品者の場合
                                ->orWhereHas('item', function($subQ) {
                                    $subQ->where('user_id', Auth::id());
                                });
                        });
                    })
                    ->with(['soldItem.item', 'soldItem.user', 'messages'])
                    ->get();

                // デバッグ用
                \Log::info('Trade Statuses:', ['count' => $tradeStatuses->count()]);

                foreach($tradeStatuses as $status) {
                    \Log::info('Trade Status:', [
                        'id' => $status->id,
                        'sold_item_id' => $status->sold_item_id,
                        'soldItem' => $status->soldItem,
                        'item' => $status->soldItem->item ?? null
                    ]);

                    if ($status->soldItem && $status->soldItem->item) {
                        $itemsWithUnreadCounts->push([
                            'item' => $status->soldItem->item,
                            'unread_count' => $status->messages()
                                ->where('user_id', '!=', Auth::id())
                                ->where('is_read', false)
                                ->count()
                        ]);
                    }
                }
                $items = $itemsWithUnreadCounts;
                break;

            case 'buy':
                $soldItems = SoldItem::where('user_id', $user->id)->get();
                foreach($soldItems as $soldItem) {
                    $itemsWithUnreadCounts->push([
                        'item' => $soldItem->item,
                        'unread_count' => 0
                    ]);
                }
                $items = $itemsWithUnreadCounts;
                break;

            default: // 'sell'
                $userItems = Item::where('user_id', $user->id)->get();
                foreach($userItems as $item) {
                    $itemsWithUnreadCounts->push([
                        'item' => $item,
                        'unread_count' => 0
                    ]);
                }
                $items = $itemsWithUnreadCounts;
                break;
        }

        // 取引中の未読メッセージ総数を取得（ページに関係なく常に取得）
        $totalUnreadCount = TradeMessage::whereHas('tradeStatus.soldItem', function($query) {
            $query->where('user_id', Auth::id())
                ->orWhereHas('item', function($q) {
                    $q->where('user_id', Auth::id());
                });
        })
        ->where('user_id', '!=', Auth::id())
        ->where('is_read', false)
        ->count();

        return view('mypage', compact('user', 'items', 'page', 'totalUnreadCount'));
    }
}
