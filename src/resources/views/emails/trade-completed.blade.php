<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
    <h2>取引が完了しました</h2>

    <p>{{ $buyer->name }}様が取引を完了しました。</p>

    <h3>取引商品情報：</h3>
    <ul>
        <li>商品名：{{ $item->name }}</li>
        <li>取引金額：{{ number_format($item->price) }}円</li>
    </ul>

    <p>取引の評価をお願いいたします。</p>

    <a href="{{ route('trade.show', $item->id) }}">取引画面へ</a>
</body>
</html>
