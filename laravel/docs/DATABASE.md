# データベース設計

## ER図

```
users
├── id
├── name
├── email
├── password
├── xrpl_address (nullable)
├── stripe_id (nullable)
└── timestamps

listings
├── id
├── seller_id (FK: users)
├── title
├── description
├── type (website/app/saas)
├── price (最低価格)
├── url (nullable)
├── monthly_revenue (nullable)
├── monthly_profit (nullable)
├── monthly_pv (nullable)
├── monthly_uu (nullable)
├── total_users (nullable)
├── dau (nullable)
├── mau (nullable)
├── total_downloads (nullable)
├── status (draft/active/sold/suspended)
└── timestamps

listing_price_plans
├── id
├── listing_id (FK: listings)
├── name
├── description (nullable)
├── price
├── includes_members (boolean)
├── includes_source (boolean)
├── includes_installation (boolean)
├── sort_order
└── timestamps

transactions
├── id
├── listing_id (FK: listings)
├── listing_price_plan_id (FK: listing_price_plans, nullable)
├── buyer_id (FK: users)
├── seller_id (FK: users)
├── amount
├── platform_fee
├── payment_method (stripe/xrpl)
├── stripe_payment_intent_id (nullable)
├── xrpl_escrow_sequence (nullable)
├── xrpl_transaction_hash (nullable)
├── xrp_amount (nullable)
├── status (pending/paid/escrowed/released/refunded)
├── paid_at (nullable)
├── released_at (nullable)
└── timestamps
```

## テーブル詳細

### users

ユーザー情報を管理。購入者と販売者の区別はなく、すべてのユーザーが両方の役割を持てる。

| カラム | 型 | 説明 |
|--------|-----|------|
| id | bigint | 主キー |
| name | varchar(255) | ユーザー名 |
| email | varchar(255) | メールアドレス（ユニーク） |
| password | varchar(255) | ハッシュ化されたパスワード |
| xrpl_address | varchar(255) | XRPLウォレットアドレス（ユニーク、nullable） |
| stripe_id | varchar(255) | Stripe顧客ID（nullable） |
| pm_type | varchar(255) | 支払い方法タイプ（nullable） |
| pm_last_four | varchar(4) | カード下4桁（nullable） |
| trial_ends_at | timestamp | トライアル終了日（nullable） |
| remember_token | varchar(100) | Remember Meトークン |
| created_at | timestamp | 作成日時 |
| updated_at | timestamp | 更新日時 |

### listings

出品情報を管理。

| カラム | 型 | 説明 |
|--------|-----|------|
| id | bigint | 主キー |
| seller_id | bigint | 販売者ID（FK: users） |
| title | varchar(255) | タイトル |
| description | text | 説明 |
| type | enum | 種類（website/app/saas） |
| price | decimal(12,2) | 最低価格 |
| url | varchar(255) | URL（nullable） |
| tech_stack | json | 技術スタック（nullable） |
| monthly_revenue | integer | 月間売上（nullable） |
| monthly_profit | integer | 月間利益（nullable） |
| monthly_pv | integer | 月間PV（nullable） |
| monthly_uu | integer | 月間UU（nullable） |
| total_users | integer | 登録ユーザー数（nullable） |
| dau | integer | DAU（nullable） |
| mau | integer | MAU（nullable） |
| total_downloads | integer | 累計ダウンロード数（nullable） |
| status | enum | ステータス（draft/active/sold/suspended） |
| created_at | timestamp | 作成日時 |
| updated_at | timestamp | 更新日時 |

### listing_price_plans

出品ごとの価格プランを管理。1つの出品に複数のプランを設定可能。

| カラム | 型 | 説明 |
|--------|-----|------|
| id | bigint | 主キー |
| listing_id | bigint | 出品ID（FK: listings） |
| name | varchar(255) | プラン名 |
| description | text | プラン説明（nullable） |
| price | decimal(12,2) | 価格 |
| includes_members | boolean | 会員データ含む |
| includes_source | boolean | ソースコード含む |
| includes_installation | boolean | 設置サポート含む |
| sort_order | integer | 表示順 |
| created_at | timestamp | 作成日時 |
| updated_at | timestamp | 更新日時 |

### transactions

取引履歴を管理。エスクロー状態も含む。

| カラム | 型 | 説明 |
|--------|-----|------|
| id | bigint | 主キー |
| listing_id | bigint | 出品ID（FK: listings） |
| listing_price_plan_id | bigint | 価格プランID（FK: listing_price_plans, nullable） |
| buyer_id | bigint | 購入者ID（FK: users） |
| seller_id | bigint | 販売者ID（FK: users） |
| amount | decimal(12,2) | 金額（JPY） |
| platform_fee | decimal(12,2) | プラットフォーム手数料 |
| payment_method | enum | 決済方法（stripe/xrpl） |
| stripe_payment_intent_id | varchar(255) | Stripe PaymentIntent ID（nullable） |
| xrpl_escrow_sequence | varchar(255) | XRPLエスクローシーケンス（nullable） |
| xrpl_transaction_hash | varchar(255) | XRPLトランザクションハッシュ（nullable） |
| xrp_amount | decimal(20,6) | XRP金額（nullable） |
| status | enum | ステータス（pending/paid/escrowed/released/refunded） |
| paid_at | timestamp | 支払い日時（nullable） |
| released_at | timestamp | 解放日時（nullable） |
| created_at | timestamp | 作成日時 |
| updated_at | timestamp | 更新日時 |

## リレーション

### User
- `hasMany` listings (seller_id)
- `hasMany` purchases (buyer_id → transactions)
- `hasMany` sales (seller_id → transactions)

### Listing
- `belongsTo` seller (User)
- `hasMany` pricePlans (ListingPricePlan)
- `hasMany` transactions

### ListingPricePlan
- `belongsTo` listing

### Transaction
- `belongsTo` listing
- `belongsTo` pricePlan (ListingPricePlan)
- `belongsTo` buyer (User)
- `belongsTo` seller (User)

## インデックス

### users
- PRIMARY KEY (id)
- UNIQUE (email)
- UNIQUE (xrpl_address)
- INDEX (stripe_id)

### listings
- PRIMARY KEY (id)
- FOREIGN KEY (seller_id) REFERENCES users(id)
- INDEX (status)
- INDEX (type)

### listing_price_plans
- PRIMARY KEY (id)
- FOREIGN KEY (listing_id) REFERENCES listings(id)
- INDEX (listing_id, sort_order)

### transactions
- PRIMARY KEY (id)
- FOREIGN KEY (listing_id) REFERENCES listings(id)
- FOREIGN KEY (listing_price_plan_id) REFERENCES listing_price_plans(id)
- FOREIGN KEY (buyer_id) REFERENCES users(id)
- FOREIGN KEY (seller_id) REFERENCES users(id)
- INDEX (status)
- INDEX (payment_method)

## ステータス遷移

### Listing Status
```
draft → active → sold
  ↓       ↓
suspended
```

### Transaction Status
```
pending → paid → escrowed → released
                    ↓
                 refunded
```

## サンプルクエリ

### 出品一覧の取得
```sql
SELECT l.*, u.name as seller_name, MIN(lpp.price) as min_price
FROM listings l
JOIN users u ON l.seller_id = u.id
LEFT JOIN listing_price_plans lpp ON l.id = lpp.listing_id
WHERE l.status = 'active'
GROUP BY l.id
ORDER BY l.created_at DESC;
```

### ユーザーの取引履歴
```sql
SELECT t.*, l.title, lpp.name as plan_name
FROM transactions t
JOIN listings l ON t.listing_id = l.id
LEFT JOIN listing_price_plans lpp ON t.listing_price_plan_id = lpp.id
WHERE t.buyer_id = ?
ORDER BY t.created_at DESC;
```

### XRPL決済の取引
```sql
SELECT t.*, u.name as buyer_name, u.xrpl_address
FROM transactions t
JOIN users u ON t.buyer_id = u.id
WHERE t.payment_method = 'xrpl'
AND t.status = 'escrowed';
```
