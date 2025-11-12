# Webサイト・アプリ売買プラットフォーム（Web3対応）

Laravel + MySQL + Stripe + XRPL で構築されたWeb3マーケットプレイス

## プロジェクト概要

このプラットフォームは、Webサイト、アプリ、SaaSサービスの売買を可能にするマーケットプレイスです。
従来のクレジットカード決済に加え、XRP Ledger（XRPL）を使用したWeb3機能を実装しています。

## ディレクトリ構造

```
.
├── README.md                          # このファイル
└── laravel/                           # Laravelアプリケーション
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
