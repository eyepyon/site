# システムアーキテクチャ

## 概要

このプラットフォームは、Laravel MVCアーキテクチャをベースに、Web3機能（XRPL統合）を追加したハイブリッド型マーケットプレイスです。

## アーキテクチャ図

```
┌─────────────────────────────────────────────────────────┐
│                    フロントエンド                          │
│                   (Blade Templates)                      │
└─────────────────────────────────────────────────────────┘
                            │
                            ↓
┌─────────────────────────────────────────────────────────┐
│                   ルーティング層                          │
│                   (routes/web.php)                       │
└─────────────────────────────────────────────────────────┘
                            │
                            ↓
┌─────────────────────────────────────────────────────────┐
│                  コントローラー層                         │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │
│  │   Listing    │  │   Payment    │  │ Transaction  │  │
│  │  Controller  │  │  Controller  │  │  Controller  │  │
│  └──────────────┘  └──────────────┘  └──────────────┘  │
│  ┌──────────────┐  ┌──────────────┐                    │
│  │     Auth     │  │   XRPL Auth  │                    │
│  │  Controller  │  │  Controller  │                    │
│  └──────────────┘  └──────────────┘                    │
└─────────────────────────────────────────────────────────┘
                            │
                            ↓
┌─────────────────────────────────────────────────────────┐
│                   サービス層                             │
│  ┌──────────────┐  ┌──────────────┐                    │
│  │   Payment    │  │     XRPL     │                    │
│  │   Service    │  │   Service    │                    │
│  └──────────────┘  └──────────────┘                    │
└─────────────────────────────────────────────────────────┘
                            │
                ┌───────────┴───────────┐
                ↓                       ↓
┌──────────────────────────┐  ┌──────────────────────────┐
│      モデル層              │  │     外部サービス          │
│  ┌────────────────┐      │  │  ┌────────────────┐    │
│  │     User       │      │  │  │     Stripe     │    │
│  ├────────────────┤      │  │  ├────────────────┤    │
│  │    Listing     │      │  │  │   XRPL Node    │    │
│  ├────────────────┤      │  │  ├────────────────┤    │
│  │ PricePlan      │      │  │  │   CoinGecko    │    │
│  ├────────────────┤      │  │  │  (XRP Price)   │    │
│  │  Transaction   │      │  │  └────────────────┘    │
│  └────────────────┘      │  └──────────────────────────┘
└──────────────────────────┘
                │
                ↓
┌─────────────────────────────────────────────────────────┐
│                   データベース層                          │
│                      (MySQL)                             │
└─────────────────────────────────────────────────────────┘
```

## レイヤー構成

### 1. プレゼンテーション層（View）

**責務**: ユーザーインターフェースの表示

**技術**: Blade テンプレートエンジン

**主要ビュー**:
- `listings/index.blade.php` - 出品一覧
- `listings/show.blade.php` - 出品詳細
- `listings/create.blade.php` - 出品作成
- `payments/checkout.blade.php` - 決済ページ（Stripe/XRPL選択）
- `transactions/show.blade.php` - 取引詳細
- `auth/xrpl-login.blade.php` - XRPLログイン

### 2. コントローラー層

**責務**: リクエスト処理とビジネスロジックの呼び出し

**主要コントローラー**:

#### ListingController
- 出品のCRUD操作
- 出品一覧の表示
- 価格プランの管理

#### PaymentController
- 決済処理の制御
- Stripe決済
- XRPL決済
- XRP価格取得

#### TransactionController
- 取引履歴の表示
- エスクロー解放処理

#### Auth/XRPLAuthController
- XRPLログインチャレンジ生成
- 署名検証
- Web3認証

### 3. サービス層

**責務**: ビジネスロジックの実装

#### PaymentService
```php
- createTransaction()        // Stripe決済トランザクション作成
- createXRPLTransaction()    // XRPL決済トランザクション作成
- releasePayment()           // 支払い解放
```

#### XRPLService
```php
- validateAddress()          // XRPLアドレス検証
- getAccountInfo()           // アカウント情報取得
- createEscrow()             // エスクロー作成
- finishEscrow()             // エスクロー完了
- cancelEscrow()             // エスクローキャンセル
- getXRPPriceInJPY()        // XRP価格取得
- convertJPYtoXRP()         // 通貨変換
- verifySignature()          // 署名検証
```

### 4. モデル層

**責務**: データベースとのやり取り、ビジネスルール

#### User
- 認証情報
- XRPLアドレス
- Stripe顧客情報
- リレーション: listings, purchases, sales

#### Listing
- 出品情報
- KPI指標
- ステータス管理
- リレーション: seller, pricePlans, transactions

#### ListingPricePlan
- 価格プラン情報
- 含まれる内容
- リレーション: listing

#### Transaction
- 取引情報
- エスクロー状態
- 決済方法（Stripe/XRPL）
- リレーション: listing, pricePlan, buyer, seller

### 5. データベース層

**技術**: MySQL

**主要テーブル**:
- users
- listings
- listing_price_plans
- transactions

## データフロー

### 出品作成フロー

```
1. User → ListingController::create()
2. ListingController → View (create.blade.php)
3. User submits form
4. ListingController::store()
5. Validation
6. Listing::create()
7. foreach plans → ListingPricePlan::create()
8. Redirect to listing detail
```

### Stripe決済フロー

```
1. User selects plan → PaymentController::checkout()
2. User enters card → Stripe.js
3. PaymentController::process()
4. PaymentService::createTransaction()
5. Stripe API call
6. Transaction::create() with stripe_payment_intent_id
7. Listing status → 'sold'
8. Redirect to transaction detail
```

### XRPL決済フロー

```
1. User selects plan → PaymentController::checkout()
2. User selects XRPL payment
3. XRPLService::getXRPPriceInJPY()
4. Display XRP amount
5. User enters XRPL address
6. PaymentController::processXRPL()
7. PaymentService::createXRPLTransaction()
8. XRPLService::createEscrow()
9. Transaction::create() with xrpl_escrow_sequence
10. Redirect to transaction detail
```

### XRPLログインフロー

```
1. User → XRPLAuthController::showLoginForm()
2. User enters XRPL address
3. XRPLAuthController::generateChallenge()
4. Generate random challenge
5. Store in session
6. User signs message with wallet
7. XRPLAuthController::verifyAndLogin()
8. XRPLService::verifySignature()
9. User::firstOrCreate()
10. Auth::login()
11. Redirect to home
```

## セキュリティ層

### 認証
- Laravel標準認証（セッションベース）
- XRPL署名ベース認証（Web3）
- CSRF保護

### 認可
- Policyクラスによるアクセス制御
- 出品者のみが自分の出品を編集可能
- 買い手のみが支払いを解放可能

### データ保護
- パスワードのハッシュ化（bcrypt）
- XRPLアドレスの検証
- 署名の検証
- SQLインジェクション対策（Eloquent ORM）

## スケーラビリティ

### 水平スケーリング
- ステートレスなアプリケーション設計
- セッションストレージの外部化（Redis推奨）
- ロードバランサーによる負荷分散

### キャッシング
- ルートキャッシュ
- 設定キャッシュ
- ビューキャッシュ
- XRP価格のキャッシング（推奨）

### データベース最適化
- インデックスの適切な設定
- クエリの最適化
- リレーションのEager Loading

## 外部サービス統合

### Stripe
- **用途**: クレジットカード決済
- **API**: Stripe PHP SDK
- **Webhook**: 決済イベントの受信

### XRPL
- **用途**: 暗号資産決済、Web3認証
- **API**: xrpl-php ライブラリ
- **ノード**: WebSocket接続

### CoinGecko
- **用途**: XRP/JPY為替レート取得
- **API**: REST API
- **頻度**: リアルタイム（キャッシング推奨）

## 環境設定

### 開発環境
- ローカルサーバー（php artisan serve）
- SQLiteまたはMySQL
- XRPLテストネット
- Stripeテストモード

### ステージング環境
- Webサーバー（Nginx/Apache）
- MySQL
- XRPLテストネット
- Stripeテストモード

### 本番環境
- Webサーバー（Nginx推奨）
- MySQL（レプリケーション推奨）
- XRPLメインネット
- Stripe本番モード
- SSL/TLS必須
- 監視・ログ収集

## 監視とログ

### アプリケーションログ
- `storage/logs/laravel.log`
- エラー、警告、情報ログ

### 決済ログ
- Stripe Webhookログ
- XRPLトランザクションログ

### パフォーマンス監視
- レスポンスタイム
- データベースクエリ時間
- 外部API呼び出し時間

## 今後の拡張性

### 機能拡張
- メッセージ機能
- レビュー・評価システム
- 検索・フィルター機能
- 通知システム

### 技術拡張
- API化（RESTful API）
- モバイルアプリ対応
- 他のブロックチェーン統合
- AI による価格推定
