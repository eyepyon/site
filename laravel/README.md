# Webサイト・アプリ売買プラットフォーム（Web3対応）

Laravel + MySQL + Stripe + XRPL で構築されたWeb3マーケットプレイス

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

## セットアップ

### 1. 依存関係のインストール
```bash
composer install
```

### 2. 環境設定
```bash
cp .env.example .env
php artisan key:generate
```

### 3. データベース設定
`.env`ファイルを編集：
```env
DB_DATABASE=marketplace
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4. Stripe設定
`.env`ファイルにStripe認証情報を追加：
```env
STRIPE_KEY=pk_test_xxxxxxxxxxxxx
STRIPE_SECRET=sk_test_xxxxxxxxxxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxxxxxxxxxx
```

Stripeアカウントは https://stripe.com で作成できます。

### 5. XRPL設定（オプション）
Web3機能を使用する場合、`.env`にXRPL設定を追加：
```env
XRPL_NETWORK=testnet
XRPL_PLATFORM_ADDRESS=rXXXXXXXXXXXXXXXXXXXXXXXXXXXX
XRPL_PLATFORM_SECRET=sXXXXXXXXXXXXXXXXXXXXXXXXXXXX
```

テストネットアカウントは https://xrpl.org/xrp-testnet-faucet.html で作成できます。

### 6. マイグレーション実行
```bash
php artisan migrate
```

### 7. サーバー起動
```bash
php artisan serve
```

アプリケーションは http://localhost:8000 でアクセス可能です。

## データベース構造

### users
- ユーザー情報（買い手・売り手共通）
- Stripe顧客ID

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

## セキュリティ

- パスワードはハッシュ化して保存
- CSRF保護
- エスクロー機能で安全な取引
- Stripe PCI準拠の決済処理

## ライセンス

このプロジェクトはMITライセンスの下で公開されています。
