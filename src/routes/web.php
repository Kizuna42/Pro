<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Requests\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\TradeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/',[ItemController::class, 'index'])->name('items.list');
Route::get('/item/{item}',[ItemController::class, 'detail'])->name('item.detail');
Route::get('/item', [ItemController::class, 'search']);

Route::middleware(['auth','verified'])->group(function () {
    Route::get('/sell',[ItemController::class, 'sellView']);
    Route::post('/sell',[ItemController::class, 'sellCreate']);
    Route::post('/item/like/{item_id}',[LikeController::class, 'create']);
    Route::post('/item/unlike/{item_id}',[LikeController::class, 'destroy']);
    Route::post('/item/comment/{item_id}',[CommentController::class, 'create']);
    Route::get('/purchase/{item_id}',[PurchaseController::class, 'index'])->middleware('purchase')->name('purchase.index');
    Route::post('/purchase/{item_id}',[PurchaseController::class, 'purchase'])->middleware('purchase');
    Route::get('/purchase/{item_id}/success', [PurchaseController::class, 'success'])->name('purchase.success');
    Route::get('/purchase/address/{item_id}',[PurchaseController::class, 'address']);
    Route::post('/purchase/address/{item_id}',[PurchaseController::class, 'updateAddress']);
    Route::get('/mypage', [UserController::class, 'mypage']);
    Route::get('/mypage/profile', [UserController::class, 'profile']);
    Route::post('/mypage/profile', [UserController::class, 'updateProfile']);
});

Route::post('login', [AuthenticatedSessionController::class, 'store'])->middleware('email');
Route::post('/register', [RegisteredUserController::class, 'store']);

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->name('verification.notice');

Route::post('/email/verification-notification', function (Request $request) {
    session()->get('unauthenticated_user')->sendEmailVerificationNotification();
    session()->put('resent', true);
    return back()->with('message', 'Verification link sent!');
})->name('verification.send');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    session()->forget('unauthenticated_user');
    return redirect('/mypage/profile');
})->name('verification.verify');

// 取引チャット関連
Route::middleware(['auth'])->group(function () {
    Route::get('/trade/{item_id}', [TradeController::class, 'show'])->name('trade.show');
    Route::post('/trade/message/{trade_status_id}', [TradeController::class, 'store'])->name('trade.message.store');
    Route::put('/trade/message/{message_id}', [TradeController::class, 'update'])->name('trade.message.update');
    Route::delete('/trade/message/{message_id}', [TradeController::class, 'destroy'])->name('trade.message.destroy');
    Route::post('/trade/{trade_status_id}/complete', [TradeController::class, 'complete'])->name('trade.complete');
});

    // 評価関連のルートを追加
    Route::post('/rating', [RatingController::class, 'store'])->name('rating.store');
