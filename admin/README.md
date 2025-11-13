# 管理画面

運営者専用の管理画面アプリケーション

## 概要

このアプリケーションは、サイト売買プラットフォームの運営者が使用する管理画面です。
フロント側（laravel/）と同じMySQLデータベースに接続し、ユーザー、出品、取引を管理します。

## 機能

### ダッシュボード
- 統計情報の表示
- 総ユーザー数、出品数、取引数
- 総売上、プラットフォーム収益
- 決済方法別統計
- 最近の取引一覧

### ユーザー管理
- ユーザー一覧
- ユーザー詳細
- ユーザー検索
- ユーザー削除

### 出品管理
- 出品一覧
- 出品詳細
- ステータス変更（draft/active/sold/suspended）
- 出品削除
- 種類・ステータスでフィルター

### 取引管理
- 取引一覧
- 取引詳細
- ステータス変更
- 決済方法でフィルター
- XRPL取引情報の表示

## セットアップ

### 1. 依存関係のインストール

```bash
cd admin
composer install
```

### 2. 環境設定

```bash
cp .env.example .env
php artisan key:generate
```

### 3. データベース設定

`.env`ファイルを編集し、フロント側と同じデータベースに接続：

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=marketplace  # フロント側と同じDB
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4. 管理者テーブルの作成

```bash
php artisan migrate
```

### 5. 管理者アカウントの作成

```bash
php artisan tinker
```

```php
App\Models\Admin::create([
    'name' => '管理者',
    'email' => 'admin@example.com',
    'password' => bcrypt('admin123')
]);
```

### 6. サーバー起動

```bash
php artisan serve --port=8001
```

管理画面は http://localhost:8001 でアクセス可能です。

## ログイン情報

デフォルトの管理者アカウント：
- メールアドレス: admin@example.com
- パスワード: admin123

**本番環境では必ず変更してください！**

## ディレクトリ構造

```
admin/
├── app/
│   ├── Http/Controllers/
│   │   ├── Auth/
│   │   │   └── AdminLoginController.php
│   │   ├── DashboardController.php
│   │   ├── AdminUserController.php
│   │   ├── AdminListingController.php
│   │   └── AdminTransactionController.php
│   └── Models/
│       ├── Admin.php
│       ├── User.php
│       ├── Listing.php
│       ├── ListingPricePlan.php
│       └── Transaction.php
├── config/
│   └── auth.php
├── database/
│   └── migrations/
│       └── 2024_01_01_000001_create_admins_table.php
├── resources/views/
│   ├── layouts/
│   │   └── app.blade.php
│   ├── auth/
│   │   └── login.blade.php
│   └── dashboard.blade.php
├── routes/
│   └── web.php
├── .env.example
├── composer.json
└── README.md
```

## 主要なルート

### 認証
- `GET /login` - ログインページ
- `POST /login` - ログイン処理
- `POST /logout` - ログアウト

### ダッシュボード
- `GET /` - ダッシュボード

### ユーザー管理
- `GET /users` - ユーザー一覧
- `GET /users/{id}` - ユーザー詳細
- `DELETE /users/{id}` - ユーザー削除

### 出品管理
- `GET /listings` - 出品一覧
- `GET /listings/{id}` - 出品詳細
- `PATCH /listings/{id}/status` - ステータス更新
- `DELETE /listings/{id}` - 出品削除

### 取引管理
- `GET /transactions` - 取引一覧
- `GET /transactions/{id}` - 取引詳細
- `PATCH /transactions/{id}/status` - ステータス更新

## データベース共有

フロント側（laravel/）と同じデータベースを使用します：

### 共有テーブル
- `users` - ユーザー情報
- `listings` - 出品情報
- `listing_price_plans` - 価格プラン
- `transactions` - 取引情報

### 管理画面専用テーブル
- `admins` - 管理者アカウント

## セキュリティ

### 認証
- 管理者専用の認証ガード（`auth:admin`）
- セッションベースの認証
- CSRF保護

### アクセス制御
- すべてのルートに認証ミドルウェア
- 管理者のみアクセス可能

### 本番環境での注意事項
1. 強力なパスワードを設定
2. HTTPS必須
3. IPアドレス制限の検討
4. 定期的なログ監視
5. 二要素認証の導入（推奨）

## トラブルシューティング

### データベース接続エラー

```
SQLSTATE[HY000] [2002] Connection refused
```

**解決方法**:
1. フロント側と同じDB設定を使用
2. MySQLが起動しているか確認
3. 認証情報が正しいか確認

### ログインできない

**解決方法**:
1. 管理者アカウントが作成されているか確認
2. パスワードが正しいか確認
3. `admins`テーブルが存在するか確認

## 今後の拡張

- [ ] 統計グラフの追加
- [ ] CSVエクスポート機能
- [ ] メール通知機能
- [ ] 監査ログ
- [ ] 二要素認証
- [ ] API管理
- [ ] 設定管理
