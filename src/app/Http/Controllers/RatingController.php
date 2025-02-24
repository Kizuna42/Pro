<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'trade_status_id' => 'required|exists:trade_statuses,id',
            'rated_user_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5'
        ]);

        Rating::create([
            'trade_status_id' => $request->trade_status_id,
            'rating_user_id' => Auth::id(),
            'rated_user_id' => $request->rated_user_id,
            'rating' => $request->rating
        ]);

        return redirect()->route('trade.show', $request->trade_status_id)
            ->with('success', '評価を送信しました');
    }
}
