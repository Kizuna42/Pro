<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\TradeStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // バリデーション
            $request->validate([
                'trade_status_id' => 'required|exists:trade_statuses,id',
                'rated_user_id' => 'required|exists:users,id',
                'rating' => 'required|integer|min:1|max:5'
            ]);

            // 評価を保存
            $rating = Rating::create([
                'trade_status_id' => $request->trade_status_id,
                'rating_user_id' => Auth::id(),
                'rated_user_id' => $request->rated_user_id,
                'rating' => $request->rating
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '評価を送信しました'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Rating error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => '評価の送信に失敗しました: ' . $e->getMessage()
            ], 500);
        }
    }
}
