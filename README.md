# フリマアプリケーション

## 概要
このアプリケーションは、ユーザー同士で商品の売買ができるフリーマーケットプラットフォームです。

## 主な機能
- ユーザー認証（会員登録・ログイン）
- 商品出品・購入
- 取引チャット
- 取引評価システム
- メール通知

# 環境構築

1. Dockerを起動する

2. プロジェクト直下で、以下のコマンドを実行する

```
make init
```
このコマンドで以下の処理が実行されます：
- Dockerコンテナのビルドと起動
- 依存パッケージのインストール
- データベースのマイグレーション
- 初期データの投入
※Makefileは実行するコマンドを省略することができる便利な設定ファイルです。コマンドの入力を効率的に行えるようになります。<br>


### 必要な環境
- Docker
- PHP 8.1
- Laravel 9.x
- MySQL 8.0
- Node.js 16.x

## メール認証
mailtrapというツールを使用しています。<br>
以下のリンクから会員登録をしてください。　<br>
https://mailtrap.io/

メールボックスのIntegrationsから 「laravel 7.x and 8.x」を選択し、　<br>
.envファイルのMAIL_MAILERからMAIL_ENCRYPTIONまでの項目をコピー＆ペーストしてください。　<br>
MAIL_FROM_ADDRESSは任意のメールアドレスを入力してください。　

## Stripeについて
コンビニ支払いとカード支払いのオプションがありますが、決済画面にてコンビニ支払いを選択しますと、レシートを印刷する画面に遷移します。そのため、カード支払いを成功させた場合に意図する画面遷移が行える想定です。<br>

1. [Stripe](https://stripe.com)でアカウントを作成
2. APIキーを取得し、`.env`ファイルに設定：
```
STRIPE_PUBLIC_KEY="パブリックキー"
STRIPE_SECRET_KEY="シークレットキー"
```

以下のリンクは公式ドキュメントです。<br>
https://docs.stripe.com/payments/checkout?locale=ja-JP

## 新規実装機能

### 1. 取引チャットシステム
- リアルタイムメッセージング
- 画像添付機能（PNG/JPEG対応）
- メッセージの編集・削除機能
- 未読メッセージ通知
- 取引履歴の自動ソート（最新メッセージ順）

### 2. 取引評価システム
- 5段階評価
- 評価平均のプロフィール表示
- 取引完了時の自動評価リクエスト
- 相互評価システム

### 3. メール通知システム
- 取引完了時の自動メール送信
- 評価依頼の通知
- カスタマイズ可能なメールテンプレート

## ER図
![alt](ER.png)

## テストアカウント
name: 一般ユーザ
email: general1@gmail.com
password: password
-------------------------
name: 一般ユーザ
email: general2@gmail.com
password: password
-------------------------

## PHPUnitを利用したテストに関して
以下のコマンド:
```
docker-compose exec php bash
php artisan migrate:fresh --env=testing
./vendor/bin/phpunit
```
※.env.testingにもStripeのAPIキーを設定してください。

## 技術スタック
- フロントエンド：Blade, JavaScript
- バックエンド：Laravel 9.x
- データベース：MySQL 8.0
- 開発環境：Docker
- 外部サービス：Stripe, Mailtrap
- テスト：PHPUnit
