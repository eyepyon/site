# API仕様

## 認証

Laravel標準のセッションベース認証を使用。

## エンドポイント一覧

### 公開エンドポイント（認証不要）

#### GET /
出品一覧を表示

**レスポンス**: HTML（Bladeビュー）

#### GET /register
会員登録フォームを表示

**レスポンス**: HTML（Bladeビュー）

#### POST /register
会員登録を実行

**リクエストボディ**:
```json
{
  "name": "山田太郎",
  "email": "yamada@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

#### GET /login
ログインフォームを表示

#### POST /login
ログインを実行

**リクエストボディ**:
```json
{
  "email": "yamada@example.com",
  "password": "password123",
  "remember": true
}
```

### 認証必須エンドポイント

#### GET /listings/{id}
出品詳細を表示

**パラメータ**:
- `id`: 出品ID

**レスポンス**: HTML（出品詳細、価格プラン一覧）

#### GET /listings/create
出品作成フォームを表示

#### POST /listings
出品を作成

**リクエストボディ**:
```json
{
  "title": "人気ブログサイト",
  "description": "月間10万PVのブログサイトです",
  "type": "website",
  "url": "https://example.com",
  "monthly_revenue": 50000,
  "monthly_profit": 30000,
  "monthly_pv": 100000,
  "monthly_uu": 30000,
  "plans": [
    {
      "name": "ソースコードのみ",
      "price": 100000,
      "description": "ソースコードのみの提供",
      "includes_source": true
    },
    {
      "name": "フルパッケージ",
      "price": 150000,
      "description": "ソースコード + 会員データ + 設置サポート",
      "includes_source": true,
      "includes_members": true,
      "includes_installation": true
    }
  ]
}
```

#### GET /listings/{id}/edit
出品編集フォームを表示

#### PUT /listings/{id}
出品を更新

#### GET /listings/{id}/checkout?plan_id={plan_id}
購入手続きページを表示

**クエリパラメータ**:
- `plan_id`: 選択した価格プランID

#### POST /listings/{id}/payment
Stripe決済を実行

**リクエストボディ**:
```json
{
  "payment_method": "pm_xxxxxxxxxxxxx",
  "plan_id": 1,
  "payment_type": "stripe"
}
```

#### POST /listings/{id}/payment/xrpl
XRPL決済を実行

**リクエストボディ**:
```json
{
  "plan_id": 1,
  "buyer_xrpl_address": "rXXXXXXXXXXXXXXXXXXXXXXXXXXXX",
  "payment_type": "xrpl"
}
```

#### GET /xrpl/price
XRP/JPYレートを取得

**レスポンス**:
```json
{
  "rate": 100.50,
  "currency": "JPY"
}
```

#### GET /transactions
取引履歴一覧を表示

#### GET /transactions/{id}
取引詳細を表示

#### POST /transactions/{id}/release
支払いを解放（買い手のみ実行可能）

## データモデル

### User
```php
{
  "id": 1,
  "name": "山田太郎",
  "email": "yamada@example.com",
  "xrpl_address": "rXXXXXXXXXXXXXXXXXXXXXXXXXXXX",
  "stripe_id": "cus_xxxxxxxxxxxxx",
  "created_at": "2024-01-01T00:00:00Z"
}
```

### Listing
```php
{
  "id": 1,
  "seller_id": 1,
  "title": "人気ブログサイト",
  "description": "月間10万PVのブログサイトです",
  "type": "website",
  "price": 100000,
  "url": "https://example.com",
  "monthly_revenue": 50000,
  "monthly_profit": 30000,
  "monthly_pv": 100000,
  "monthly_uu": 30000,
  "status": "active",
  "created_at": "2024-01-01T00:00:00Z"
}
```

### ListingPricePlan
```php
{
  "id": 1,
  "listing_id": 1,
  "name": "ソースコードのみ",
  "description": "ソースコードのみの提供",
  "price": 100000,
  "includes_members": false,
  "includes_source": true,
  "includes_installation": false,
  "sort_order": 0
}
```

### Transaction
```php
{
  "id": 1,
  "listing_id": 1,
  "listing_price_plan_id": 1,
  "buyer_id": 2,
  "seller_id": 1,
  "amount": 100000,
  "platform_fee": 10000,
  "payment_method": "xrpl",
  "stripe_payment_intent_id": null,
  "xrpl_escrow_sequence": 12345,
  "xrpl_transaction_hash": "ABC123...",
  "xrp_amount": 1000.123456,
  "status": "escrowed",
  "paid_at": "2024-01-01T00:00:00Z",
  "released_at": null
}
```

## ステータス

### Listing Status
- `draft`: 下書き
- `active`: 公開中
- `sold`: 売却済み
- `suspended`: 停止中

### Transaction Status
- `pending`: 保留中
- `paid`: 支払い済み
- `escrowed`: エスクロー中
- `released`: 解放済み
- `refunded`: 返金済み
