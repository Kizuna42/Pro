@extends('layouts.default')

<!-- タイトル -->
@section('title','マイページ')

<!-- css読み込み -->
@section('css')
<link rel="stylesheet" href="{{ asset('/css/index.css')  }}" >
<link rel="stylesheet" href="{{ asset('/css/mypage.css')  }}" >
@endsection

<!-- 本体 -->
@section('content')

@include('components.header')
<div class="container">
    <div class="user">
        <div class="user__info">
            <div class="user__img">
                @if (isset($user->profile->img_url))
                    <img class="user__icon" src="{{ \Storage::url($user->profile->img_url) }}" alt="">
                @else
                    <img id="myImage" class="user__icon" src="{{ asset('img/icon.png') }}" alt="">
                @endif
            </div>
            <div class="user__details">
                <p class="user__name">{{$user->name}}</p>
                @if($user->averageRating)
                <div class="user__rating">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= $user->averageRating)
                            <i class="fas fa-star"></i>
                        @else
                            <i class="far fa-star"></i>
                        @endif
                    @endfor
                    <span>({{ number_format($user->averageRating, 1) }})</span>
                </div>
                @endif
            </div>
        </div>
        <div class="mypage__user--btn">
            <a class="btn2" href="/mypage/profile">プロフィールを編集</a>
        </div>
    </div>
    <div class="border">
        <ul class="border__list">
            <li><a href="/mypage?page=sell" class="{{ request()->get('page') == 'sell' ? 'active' : '' }}">出品した商品</a></li>
            <li><a href="/mypage?page=buy" class="{{ request()->get('page') == 'buy' ? 'active' : '' }}">購入した商品</a></li>
            <li><a href="/mypage?page=trading" class="{{ request()->get('page') == 'trading' ? 'active' : '' }}">
                取引中の商品
                @if(isset($totalUnreadCount) && $totalUnreadCount > 0)
                    <span class="badge">{{ $totalUnreadCount }}</span>
                @endif
            </a></li>
        </ul>
    </div>
    <div class="items">
        @foreach ($items as $item)
        <div class="item">
            <a href="{{ request()->get('page') === 'trading' ? '/trade/'.$item['item']->id : '/item/'.$item['item']->id }}">
                @if(request()->get('page') === 'trading' && $item['unread_count'] > 0)
                <div class="item__badge">
                    <span class="item__unread-count">{{ $item['unread_count'] }}</span>
                </div>
                @endif
                <div class="item__img--container {{ $item['item']->isSold() ? 'sold' : '' }}">
                    <img src="{{ Storage::url($item['item']->img_url) }}" class="item__img" alt="商品画像">
                </div>
                <p class="item__name">{{ $item['item']->name }}</p>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endsection
