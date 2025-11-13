# Webサイト・アプリ売買プラットフォーム（Web3対応）

Laravel + MySQL + Stripe + XRPL で構築されたWeb3マーケットプレイス

## プロジェクト概要

このプラットフォームは、Webサイト、アプリ、SaaSサービスの売買を可能にするマーケットプレイスです。
従来のクレジットカード決済に加え、XRP Ledger（XRPL）を使用したWeb3機能を実装しています。

## ディレクトリ構造

```
.
├── README.md                          # このファイル
├── laravel/                           # フロントエンド（ユーザー向け）
└── admin/                             # 管理画面（運営者向け）
```

### laravel/ - フロントエンド
    ├── app/
    │   ├── Http/Controllers/
    │   │   ├── Auth/
    │   │   │   ├── LoginController.php
    │   │   │   ├── RegisterController.php
    │   │   │   └── XRPLAuthController.php    # XRPL認証
    │   │   ├── ListingController.php
    │   │   ├── PaymentController.php
    │   │   └── TransactionController.php
    │   ├── Models/
    │   │   ├── User.php
    │   │   ├── Listing.php
    │   │   ├── ListingPricePlan.php
    │   │   └── Transaction.php
    │   └── Services/
    │       ├── PaymentService.php
    │       └── XRPLService.php               # XRPL統合
    ├── config/
    │   ├── cashier.php                       # Stripe設定
    │   └── xrpl.php                          # XRPL設定
    ├── database/
    │   └── migrations/
    │       ├── 2024_01_01_000001_create_users_table.php
    │       ├── 2024_01_01_000002_create_listings_table.php
    │       ├── 2024_01_01_000002_create_listing_price_plans_table.php
    │       └── 2024_01_01_000003_create_transactions_table.php
    ├── resources/views/
    │   ├── auth/
    │   │   ├── login.blade.php
    │   │   ├── register.blade.php
    │   │   └── xrpl-login.blade.php          # XRPLログイン
    │   ├── listings/
    │   │   ├── index.blade.php
    │   │   ├── show.blade.php
    │   │   └── create.blade.php
    │   ├── payments/
    │   │   └── checkout.blade.php            # Stripe/XRPL決済
    │   └── transactions/
    │       ├── index.blade.php
    │       └── show.blade.php
    ├── routes/
    │   └── web.php
    ├── docs/
    │   ├── SETUP.md                          # セットアップガイド
    │   ├── API.md                            # API仕様
    │   ├── FEATURES.md                       # 機能詳細
    │   └── XRPL.md                           # XRPL統合ガイド
    ├── composer.json
    ├── .env.example
    └── README.md
```

## 主要機能

### 🔓 認証不要
- **出品一覧の閲覧**: 誰でもどんなサイト・アプリが出品されているか確認可能

### 🔐 会員登録後（無料）
- **購入者として**: サイト/アプリの詳細閲覧・購入
- **販売者として**: サイト/アプリの出品・管理
- **取引管理**: 購入履歴・売上管理

### 💰 複数価格プラン
出品時に複数の価格プランを設定可能：
- ソースコードのみ
- ソースコード + 会員データ
- フルパッケージ（ソース + 会員 + 設置サポート）

### 📊 詳細な指標管理

**Webサイト向け:**
- 月間PV（ページビュー）
- 月間UU（ユニークユーザー）
- 月間売上・利益

**アプリ向け:**
- 登録ユーザー数
- DAU（デイリーアクティブユーザー）
- MAU（マンスリーアクティブユーザー）
- 累計ダウンロード数
- 月間売上・利益

**SaaS向け:**
- 上記すべての指標

### 🔒 エスクロー機能
- **Stripe**: 購入時に資金を一時保管
- **XRPL**: ブロックチェーンベースのスマートエスクロー
- 買い手が商品確認後に売り手へ支払い解放
- プラットフォーム手数料: 10%

### 🌐 Web3機能（XRPL統合）
- **XRPLウォレットログイン**: 秘密鍵不要の安全な認証
- **XRPL決済**: XRPでの購入が可能
- **XRPLエスクロー**: ブロックチェーン上での安全な取引
- **リアルタイム為替**: XRP/JPYレートの自動取得

## クイックスタート

### 必要な環境
- PHP 8.1以上
- Composer
- MySQL 5.7以上
- Node.js（オプション）

### インストール

```bash
# 1. リポジトリのクローン
cd laravel

# 2. 依存関係のインストール
composer install

# 3. 環境ファイルの設定
cp .env.example .env
php artisan key:generate

# 4. データベース作成
mysql -u root -p
CREATE DATABASE marketplace CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# 5. .envファイルを編集
# DB_DATABASE=marketplace
# DB_USERNAME=root
# DB_PASSWORD=your_password

# 6. マイグレーション実行
php artisan migrate

# 7. サーバー起動
php artisan serve
```

アプリケーションは http://localhost:8000 でアクセス可能です。

## 決済設定

### Stripe設定

1. [Stripe](https://stripe.com)でアカウント作成
2. `.env`に認証情報を追加：

```env
STRIPE_KEY=pk_test_xxxxxxxxxxxxx
STRIPE_SECRET=sk_test_xxxxxxxxxxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxxxxxxxxxx
```

### XRPL設定（オプション）

Web3機能を使用する場合：

```env
XRPL_NETWORK=testnet
XRPL_PLATFORM_ADDRESS=rXXXXXXXXXXXXXXXXXXXXXXXXXXXX
XRPL_PLATFORM_SECRET=sXXXXXXXXXXXXXXXXXXXXXXXXXXXX
```

テストネットアカウントは [XRPL Testnet Faucet](https://xrpl.org/xrp-testnet-faucet.html) で作成できます。

## 使い方

### 出品者として

1. **会員登録**（無料）
2. **出品作成**
   - タイトル、説明、種類を入力
   - KPI指標を入力（PV、ユーザー数など）
   - 複数の価格プランを設定
3. **出品を公開**（status: active）
4. **購入されたら商品を引き渡し**
5. **買い手が支払いを解放したら入金**

### 購入者として

1. **会員登録**（無料）または**XRPLウォレットでログイン**
2. **出品一覧から興味のある案件を探す**
3. **詳細ページで価格プランを選択**
4. **決済方法を選択**
   - 💳 クレジットカード（Stripe）
   - 🌐 XRPL（暗号資産）
5. **商品を受け取ったら支払いを解放**

## 主要なエンドポイント

### 公開（認証不要）
- `GET /` - 出品一覧
- `GET /register` - 会員登録
- `GET /login` - ログイン
- `GET /xrpl/login` - XRPLログイン

### 認証必須
- `GET /listings/{id}` - 出品詳細
- `GET /listings/create` - 出品作成フォーム
- `POST /listings` - 出品作成
- `GET /listings/{id}/edit` - 出品編集
- `GET /listings/{id}/checkout?plan_id={plan_id}` - 購入手続き
- `POST /listings/{id}/payment` - Stripe決済実行
- `POST /listings/{id}/payment/xrpl` - XRPL決済実行
- `GET /transactions` - 取引履歴
- `GET /transactions/{id}` - 取引詳細
- `POST /transactions/{id}/release` - 支払い解放

## データベース構造

### users
- ユーザー情報（買い手・売り手共通）
- Stripe顧客ID
- XRPLアドレス

### listings
- 出品情報
- 種類（website/app/saas）
- 各種KPI指標

### listing_price_plans
- 出品ごとの複数価格プラン
- 含まれる内容（会員データ、ソース、設置サポート）

### transactions
- 取引履歴
- エスクロー状態管理
- 選択された価格プラン
- 決済方法（Stripe/XRPL）

## 技術スタック

- **フレームワーク**: Laravel 10
- **データベース**: MySQL
- **決済**: 
  - Stripe + Laravel Cashier（クレジットカード）
  - XRPL（暗号資産）
- **ブロックチェーン**: XRP Ledger
- **フロントエンド**: Blade テンプレート
- **認証**: 
  - Laravel標準認証
  - XRPL署名ベース認証（Web3）

## ドキュメント

- [セットアップガイド](laravel/docs/SETUP.md) - 詳細なインストール手順
- [API仕様](laravel/docs/API.md) - エンドポイント一覧
- [機能詳細](laravel/docs/FEATURES.md) - 各機能の説明
- [XRPL統合ガイド](laravel/docs/XRPL.md) - Web3機能の使い方

## セキュリティ

- パスワードはハッシュ化して保存
- CSRF保護
- エスクロー機能で安全な取引
- Stripe PCI準拠の決済処理
- XRPL署名ベース認証

## ライセンス

このプロジェクトはMITライセンスの下で公開されています。

## サポート

問題が発生した場合は、以下を確認してください：
- [トラブルシューティング](laravel/docs/SETUP.md#トラブルシューティング)
- [XRPL FAQ](laravel/docs/XRPL.md#よくある質問)

## 貢献

プルリクエストを歓迎します。大きな変更の場合は、まずissueを開いて変更内容を議論してください。


### admin/ - 管理画面

運営者専用の管理画面アプリケーション。フロント側と同じデータベースに接続。

```
admin/
├── app/
│   ├── Http/Controllers/
│   │   ├── Auth/AdminLoginController.php
│   │   ├── DashboardController.php
│   │   ├── AdminUserController.php
│   │   ├── AdminListingController.php
│   │   └── AdminTransactionController.php
│   └── Models/
│       ├── Admin.php                  # 管理者モデル
│       ├── User.php                   # フロント側と共有
│       ├── Listing.php                # フロント側と共有
│       └── Transaction.php            # フロント側と共有
├── resources/views/
│   ├── layouts/app.blade.php
│   ├── auth/login.blade.php
│   └── dashboard.blade.php
├── routes/web.php
├── .env.example
└── README.md
```

## 管理画面のセットアップ

### 1. 管理画面のインストール

```bash
cd admin
composer install
cp .env.example .env
php artisan key:generate
```

### 2. データベース設定

フロント側と同じデータベースに接続：

```env
DB_DATABASE=marketplace  # フロント側と同じ
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 3. 管理者テーブルの作成

```bash
php artisan migrate
```

### 4. 管理者アカウントの作成

```bash
php artisan db:seed --class=AdminSeeder
```

または手動で：

```bash
php artisan tinker
```

```php
App\Models\Admin::create([
    'name' => '管理者',
    'email' => 'admin@example.com',
    'password' => bcrypt('your-secure-password')
]);
```

### 5. 管理画面の起動

```bash
php artisan serve --port=8001
```

管理画面は http://localhost:8001 でアクセス可能です。

### デフォルトログイン情報

- メールアドレス: admin@example.com
- パスワード: admin123

**本番環境では必ず変更してください！**

## 管理画面の機能

### 📊 ダッシュボード
- 統計情報の表示
- 総ユーザー数、出品数、取引数
- 総売上、プラットフォーム収益
- 決済方法別統計
- 最近の取引一覧

### 👥 ユーザー管理
- ユーザー一覧・検索
- ユーザー詳細（出品・購入履歴）
- ユーザー削除

### 📝 出品管理
- 出品一覧・検索
- 出品詳細
- ステータス変更（draft/active/sold/suspended）
- 出品削除

### 💰 取引管理
- 取引一覧・検索
- 取引詳細
- ステータス変更
- XRPL取引情報の表示

## アーキテクチャ

### データベース共有

```
┌─────────────────┐         ┌─────────────────┐
│   フロント側     │         │    管理画面      │
│   (laravel/)    │         │    (admin/)     │
│   Port: 8000    │         │   Port: 8001    │
└────────┬────────┘         └────────┬────────┘
         │                           │
         └───────────┬───────────────┘
                     │
              ┌──────▼──────┐
              │   MySQL     │
              │ marketplace │
              └─────────────┘
```

### 共有テーブル
- `users` - ユーザー情報
- `listings` - 出品情報
- `listing_price_plans` - 価格プラン
- `transactions` - 取引情報

### 管理画面専用テーブル
- `admins` - 管理者アカウント

## 本番環境での運用

### フロント側
```bash
cd laravel
php artisan serve --host=0.0.0.0 --port=8000
```

### 管理画面
```bash
cd admin
php artisan serve --host=0.0.0.0 --port=8001
```

### Nginxでの設定例

```nginx
# フロント側
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/laravel/public;
    # ... 省略 ...
}

# 管理画面
server {
    listen 80;
    server_name admin.yourdomain.com;
    root /var/www/admin/public;
    # ... 省略 ...
}
```

### セキュリティ推奨事項

1. **管理画面のアクセス制限**
   - IPアドレス制限
   - VPN経由のみアクセス可能に
   - Basic認証の追加

2. **強力な認証**
   - 強力なパスワードポリシー
   - 二要素認証の導入
   - セッションタイムアウトの設定

3. **監視とログ**
   - アクセスログの監視
   - 不正ログイン試行の検知
   - 重要操作の監査ログ
