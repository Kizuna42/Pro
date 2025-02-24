<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AddressRequest;
use App\Models\Item;
use App\Models\User;
use App\Models\SoldItem;
use App\Models\Profile;
use Stripe\StripeClient;
use App\Models\TradeStatus;

class PurchaseController extends Controller
{
    public function index($item_id, Request $request){
        $item = Item::find($item_id);
        $user = User::find(Auth::id());
        return view('purchase',compact('item','user'));
    }

    public function purchase($item_id, Request $request){
        $item = Item::find($item_id);
        $stripe = new StripeClient(config('stripe.stripe_secret_key'));

        [
            $user_id,
            $amount,
            $sending_postcode,
            $sending_address,
            $sending_building
        ] = [
            Auth::id(),
            $item->price,
            $request->destination_postcode,
            urlencode($request->destination_address),
            urlencode($request->destination_building) ?? null
        ];

        // success_urlをroute()ヘルパーを使用して生成
        $success_url = route('purchase.success', [
            'item_id' => $item_id,
            'user_id' => $user_id,
            'amount' => $amount,
            'sending_postcode' => $sending_postcode,
            'sending_address' => $sending_address,
            'sending_building' => $sending_building
        ]);

        $checkout_session = $stripe->checkout->sessions->create([
            'payment_method_types' => [$request->payment_method],
            'payment_method_options' => [
                'konbini' => [
                    'expires_after_days' => 7,
                ],
            ],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'jpy',
                        'product_data' => ['name' => $item->name],
                        'unit_amount' => $item->price,
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'success_url' => $success_url,
        ]);

        return redirect($checkout_session->url);
    }

    public function success($item_id, Request $request)
    {
        // クエリパラメータの検証
        if(!$request->user_id || !$request->amount || !$request->sending_postcode || !$request->sending_address){
            throw new Exception("You need all Query Parameters (user_id, amount, sending_postcode, sending_address)");
        }

        try {
            $stripe = new StripeClient(config('stripe.stripe_secret_key'));

            // Stripe決済処理
            $stripe->charges->create([
                'amount' => $request->amount,
                'currency' => 'jpy',
                'source' => 'tok_visa',
            ]);

            // 購入情報を保存
            $soldItem = SoldItem::create([
                'user_id' => $request->user_id,
                'item_id' => $item_id,
                'sending_postcode' => $request->sending_postcode,
                'sending_address' => urldecode($request->sending_address),
                'sending_building' => $request->sending_building ? urldecode($request->sending_building) : null
            ]);

            // 取引ステータスを作成
            TradeStatus::create([
                'sold_item_id' => $soldItem->item_id,
                'is_completed' => false
            ]);

            // 商品を売り切れ状態に
            $item = Item::find($item_id);
            $item->update(['is_sold' => true]);

            return redirect('/trade/' . $item_id)->with('success', '商品を購入しました');

        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return redirect()->back()->with('error', '購入処理に失敗しました。もう一度お試しください。');
        }
    }

    public function address($item_id, Request $request){
        $user = User::find(Auth::id());
        return view('address', compact('user','item_id'));
    }

    public function updateAddress(AddressRequest $request){

        $user = User::find(Auth::id());
        Profile::where('user_id', $user->id)->update([
            'postcode' => $request->postcode,
            'address' => $request->address,
            'building' => $request->building
        ]);

        return redirect()->route('purchase.index', ['item_id' => $request->item_id]);
    }

    public function store(Request $request)
    {
        try {
            // Stripeの処理...

            // 購入処理が成功したら
            $soldItem = SoldItem::create([
                'item_id' => $item->id,
                'user_id' => Auth::id(),
                'stripe_payment_id' => $payment->id
            ]);

            // 取引ステータスを作成
            TradeStatus::create([
                'sold_item_id' => $soldItem->id,
                'is_completed' => false
            ]);

            // 商品を売り切れ状態に
            $item->update(['is_sold' => true]);

            return redirect('/trade/' . $item->id)->with('success', '商品を購入しました');

        } catch (\Exception $e) {
            // エラー処理...
        }
    }
}
