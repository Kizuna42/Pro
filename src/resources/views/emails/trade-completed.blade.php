@component('mail::message')
# 取引が完了しました

{{ $tradeStatus->soldItem->item->name }}の取引が完了しました。
購入者からの評価をお待ちください。

@component('mail::button', ['url' => route('trade.show', $tradeStatus->soldItem->item_id)])
取引画面を確認する
@endcomponent

ご利用ありがとうございました。

@endcomponent
