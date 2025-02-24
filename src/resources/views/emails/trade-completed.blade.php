<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; margin: 0 auto; padding: 20px; text-align: center;">
    <h2>取引が完了しました</h2>

    <p>{{ $buyer->name }}様との取引が完了しました。</p>

    <h3>取引商品情報：</h3>
    <ul style="list-style-type: none; padding: 0;">
        <li>商品名：{{ $item->name }}</li>
        <li>取引金額：{{ number_format($item->price) }}円</li>
    </ul>

    <p>取引相手の評価をお願いいたします。</p>
    <p>以下のリンクから取引画面へ移動し、評価を行うことができます。</p>

    <div style="margin: 20px 0;">
        <a href="{{ route('trade.show', ['item_id' => $item->id, 'show_rating' => true]) }}"
            style="background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
            取引画面へ移動して評価する
        </a>
    </div>
</body>
</html>
