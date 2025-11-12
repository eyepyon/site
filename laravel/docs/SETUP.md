# セットアップガイド

## 必要な環境

- PHP 8.1以上
- Composer
- MySQL 5.7以上 または MariaDB 10.3以上
- Node.js（フロントエンド開発時）

## インストール手順

### 1. リポジトリのクローン

```bash
git clone <repository-url>
cd laravel
```

### 2. 依存関係のインストール

```bash
composer install
```

### 3. 環境ファイルの設定

```bash
cp .env.example .env
```

`.env`ファイルを編集：

```env
APP_NAME="サイト売買プラットフォーム"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# データベース設定
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=marketplace
DB_USERNAME=root
DB_PASSWORD=your_password

# Stripe設定
STRIPE_KEY=pk_test_xxxxxxxxxxxxx
STRIPE_SECRET=sk_test_xxxxxxxxxxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxxxxxxxxxx

# 通貨設定
CASHIER_CURRENCY=jpy
CASHIER_CURRENCY_LOCALE=ja_JP
```

### 4. アプリケーションキーの生成

```bash
php artisan key:generate
```

### 5. データベースの作成

MySQLにログイン：
```bash
mysql -u root -p
```

データベースを作成：
```sql
CREATE DATABASE marketplace CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### 6. マイグレーションの実行

```bash
php artisan migrate
```

### 7. Stripeの設定

1. [Stripe](https://stripe.com)でアカウントを作成
2. ダッシュボードから「開発者」→「APIキー」を開く
3. 公開可能キー（pk_test_...）と秘密キー（sk_test_...）をコピー
4. `.env`ファイルに貼り付け

#### Webhookの設定（本番環境）

1. Stripeダッシュボードで「開発者」→「Webhook」を開く
2. 「エンドポイントを追加」をクリック
3. エンドポイントURL: `https://yourdomain.com/stripe/webhook`
4. イベント選択: `payment_intent.*`を選択
5. Webhook署名シークレットをコピーして`.env`に設定

### 8. サーバーの起動

```bash
php artisan serve
```

アプリケーションは http://localhost:8000 でアクセス可能です。

## 開発環境のセットアップ

### Laravel Sailを使用する場合

```bash
# Sailのインストール
composer require laravel/sail --dev

# Sailの初期化
php artisan sail:install

# Sailの起動
./vendor/bin/sail up -d

# マイグレーション
./vendor/bin/sail artisan migrate
```

## テストデータの作成

### シーダーの作成（オプション）

```bash
php artisan make:seeder UserSeeder
php artisan make:seeder ListingSeeder
```

シーダーを実行：
```bash
php artisan db:seed
```

## トラブルシューティング

### データベース接続エラー

```
SQLSTATE[HY000] [2002] Connection refused
```

**解決方法**:
1. MySQLが起動しているか確認
2. `.env`のDB設定が正しいか確認
3. データベースが作成されているか確認

### Stripeエラー

```
No API key provided
```

**解決方法**:
1. `.env`にStripeキーが設定されているか確認
2. キャッシュをクリア: `php artisan config:clear`

### マイグレーションエラー

```
Syntax error or access violation
```

**解決方法**:
1. データベースの文字コードを確認
2. MySQLのバージョンを確認（5.7以上）
3. マイグレーションファイルの順序を確認

## 本番環境へのデプロイ

### 1. 環境変数の設定

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

### 2. 最適化

```bash
# 設定キャッシュ
php artisan config:cache

# ルートキャッシュ
php artisan route:cache

# ビューキャッシュ
php artisan view:cache

# Composerの最適化
composer install --optimize-autoloader --no-dev
```

### 3. パーミッションの設定

```bash
chmod -R 755 storage bootstrap/cache
```

### 4. Webサーバーの設定

Nginxの設定例：
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/laravel/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 5. SSL証明書の設定

Let's Encryptを使用：
```bash
sudo certbot --nginx -d yourdomain.com
```

## メンテナンス

### ログの確認

```bash
tail -f storage/logs/laravel.log
```

### キャッシュのクリア

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### データベースのバックアップ

```bash
mysqldump -u root -p marketplace > backup.sql
```
