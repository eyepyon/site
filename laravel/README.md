# Webサイト・アプリ売買プラットフォーム

Laravel + MySQL + Stripe (Laravel Cashier) で構築されたマーケットプレイス

## 主要機能

- ユーザー認証（買い手・売り手）
- サイト/アプリの出品・閲覧
- Stripe決済統合
- エスクロー機能
- 取引管理

## セットアップ

1. 依存関係のインストール:
```bash
composer install
```

2. 環境設定:
```bash
cp .env.example .env
php artisan key:generate
```

3. データベース設定（.envファイルを編集）:
```
DB_DATABASE=marketplace
DB_USERNAME=root
DB_PASSWORD=your_password
```

4. Stripe設定（.envファイルを編集）:
```
STRIPE_KEY=your_stripe_publishable_key
STRIPE_SECRET=your_stripe_secret_key
```

5. マイグレーション実行:
```bash
php artisan migrate
```

6. サーバー起動:
```bash
php artisan serve
```

## 主要なエンドポイント

- `GET /` - 出品一覧
- `GET /listings/{id}` - 出品詳細
- `POST /listings` - 出品作成
- `GET /listings/{id}/checkout` - 購入手続き
- `GET /transactions` - 取引履歴

## 技術スタック

- Laravel 10
- MySQL
- Stripe + Laravel Cashier
- Blade テンプレート
