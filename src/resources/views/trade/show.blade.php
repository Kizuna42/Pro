@extends('layouts.default')

@section('title', '取引チャット')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/trade.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endsection

@section('content')
@include('components.header')

<div class="trade">
    <div class="trade__sidebar">
        <h3 class="trade__sidebar-title">その他の取引</h3>
        <div class="trade__list">
            @foreach($otherTrades as $trade)
            <a href="/trade/{{ $trade->soldItem->item->id }}" class="trade__item">
                <div class="trade__item-img">
                    <img src="{{ Storage::url($trade->soldItem->item->img_url) }}" alt="商品画像">
                </div>
                <div class="trade__item-info">
                    <p class="trade__item-name">{{ $trade->soldItem->item->name }}</p>
                    <p class="trade__item-price">{{ number_format($trade->soldItem->item->price) }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>

    <div class="trade__main">
        <div class="trade__title-container">
            <div class="trade__title-left">
                <div class="trade__user-icon">
                    <img src="{{ $soldItem->user->profile->img_url ? Storage::url($soldItem->user->profile->img_url) : asset('img/icon.png') }}" alt="ユーザーアイコン">
                </div>
                <h1 class="trade__title">{{ $soldItem->user->name }}さんとの取引画面</h1>
            </div>
            @if(Auth::id() === $soldItem->user_id && !$tradeStatus->is_completed)
            <form id="completeForm" action="{{ route('trade.complete', $tradeStatus->id) }}" method="POST" class="d-inline">
                @csrf
                <button id="completeBtn" type="button" class="trade__complete-btn" onclick="showRatingModal()">
                    取引を完了する
                </button>
            </form>
            @endif
        </div>
        <div class="trade__header">
            <div class="trade__item-detail">
                <div class="trade__item-img">
                    <img src="{{ Storage::url($item->img_url) }}" alt="商品画像">
                </div>
                <div class="trade__item-info">
                    <h2 class="trade__item-name">{{ $item->name }}</h2>
                    <p class="trade__item-price">{{ number_format($item->price) }}</p>
                </div>
            </div>
        </div>

        <div class="trade__messages" id="messages">
            @foreach($tradeStatus->messages as $message)
            <div class="message {{ $message->user_id === Auth::id() ? 'message--mine' : '' }}">
                <div class="message__user">
                    <img src="{{ $message->user->profile->img_url ? Storage::url($message->user->profile->img_url) : asset('img/icon.png') }}" alt="ユーザー画像">
                </div>
                <div class="message__content">
                    @if($message->image_url)
                    <div class="message__image">
                        <a href="{{ Storage::url($message->image_url) }}" target="_blank">
                            <img src="{{ Storage::url($message->image_url) }}" alt="添付画像">
                        </a>
                    </div>
                    @endif
                    <p class="message__text">{{ $message->message }}</p>
                    @if($message->user_id === Auth::id())
                    <div class="message__actions">
                        <button class="message__edit" onclick="editMessage({{ $message->id }}, '{{ $message->message }}')">
                            <i class="fas fa-edit"></i> 編集
                        </button>
                        <form action="/trade/message/{{ $message->id }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="message__delete">
                                <i class="fas fa-trash"></i> 削除
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <form action="{{ route('trade.message.store', $tradeStatus->id) }}" method="POST" class="trade__form" enctype="multipart/form-data">
            @csrf
            <div class="trade__input-container">
                <textarea name="message" class="trade__input" placeholder="取引メッセージを記入してください">{{ old('message') }}</textarea>
                <div class="trade__actions">
                    <label class="btn2">
                        <input type="file" name="image" accept="image/*" onchange="previewImage(this)" style="display: none;">
                        <i class="fas fa-image"></i> 画像を追加
                    </label>
                    <button type="submit" class="trade__submit">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
            <div id="image-preview" class="trade__preview"></div>
            @error('message')
            <div class="trade__error">{{ $message }}</div>
            @enderror
            @error('image')
            <div class="trade__error">{{ $message }}</div>
            @enderror
        </form>
    </div>
</div>

<!-- 評価モーダル -->
<div id="ratingModal" class="modal">
    <div class="modal__content">
        <h2>取引が完了しました</h2>
        <p>今回の取引相手はどうでしたか？</p>

        <form id="ratingForm" action="{{ route('rating.store') }}" method="POST">
            @csrf
            <input type="hidden" name="trade_status_id" value="{{ $tradeStatus->id }}">
            <input type="hidden" name="rated_user_id" value="{{ $item->user_id }}">

            <div class="rating">
                <input type="radio" id="star5" name="rating" value="5">
                <label for="star5"><i class="fas fa-star"></i></label>
                <input type="radio" id="star4" name="rating" value="4">
                <label for="star4"><i class="fas fa-star"></i></label>
                <input type="radio" id="star3" name="rating" value="3">
                <label for="star3"><i class="fas fa-star"></i></label>
                <input type="radio" id="star2" name="rating" value="2">
                <label for="star2"><i class="fas fa-star"></i></label>
                <input type="radio" id="star1" name="rating" value="1" required>
                <label for="star1"><i class="fas fa-star"></i></label>
            </div>

            <div class="rating-submit-container">
                <button type="submit" class="rating-submit-btn">送信する</button>
            </div>
        </form>
    </div>
</div>

<script>
function editMessage(id, text) {
    const textarea = document.querySelector('.trade__input');
    textarea.value = text;
    textarea.focus();

    const form = document.querySelector('.trade__form');
    form.action = `/trade/message/${id}`;

    const method = document.createElement('input');
    method.type = 'hidden';
    method.name = '_method';
    method.value = 'PUT';
    form.appendChild(method);
}

// メッセージ一覧を一番下までスクロール
const messages = document.getElementById('messages');
messages.scrollTop = messages.scrollHeight;

function previewImage(input) {
    const preview = document.getElementById('image-preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `
                <img src="${e.target.result}" alt="プレビュー">
                <button onclick="removePreview()" class="preview__remove">×</button>
            `;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function removePreview() {
    const preview = document.getElementById('image-preview');
    preview.innerHTML = '';
    document.querySelector('input[type="file"]').value = '';
}

// モーダルを表示する関数
function showRatingModal() {
    const modal = document.getElementById('ratingModal');
    if (modal) {
        modal.classList.add('active');
    }
}

// 取引完了ボタンのクリックイベント
document.addEventListener('DOMContentLoaded', () => {
    const completeBtn = document.getElementById('completeBtn');
    const completeForm = document.getElementById('completeForm');

    if (completeBtn && completeForm) {
        completeBtn.addEventListener('click', (e) => {
            e.preventDefault();

            if (confirm('取引を完了しますか？')) {
                const formAction = completeForm.getAttribute('action');
                const csrfToken = document.querySelector('meta[name="csrf-token"]');

                if (!csrfToken) {
                    console.error('CSRF トークンが見つかりません');
                    alert('セッションエラーが発生しました。ページを更新してください。');
                    return;
                }

                fetch(formAction, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                    },
                    body: JSON.stringify({
                        _token: csrfToken.getAttribute('content')
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showRatingModal();
                    } else {
                        throw new Error(data.message || '取引完了処理に失敗しました');
                    }
                })
                .catch(error => {
                    console.error('エラー:', error);
                    alert('取引完了処理中にエラーが発生しました。ページを更新して再度お試しください。');
                });
            }
        });
    }
});
</script>
@endsection
