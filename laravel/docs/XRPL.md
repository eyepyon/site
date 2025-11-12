# XRPL統合ガイド

## 概要

このプラットフォームはXRP Ledger（XRPL）と統合されており、Web3機能を提供します。

## 主要機能

### 1. XRPLウォレットログイン

秘密鍵を共有せずに、署名ベースの認証でログインできます。

#### 仕組み

1. ユーザーがXRPLアドレスを入力
2. サーバーがチャレンジメッセージを生成
3. ユーザーがウォレットでメッセージに署名
4. サーバーが署名を検証してログイン

#### 使用方法

```
1. ログインページで「XRPLウォレットでログイン」をクリック
2. XRPLアドレスを入力
3. 表示されたメッセージをウォレットで署名
4. 署名と公開鍵を入力してログイン
```

### 2. XRPL決済

XRPで商品を購入できます。

#### 特徴

- **リアルタイム為替**: XRP/JPYレートを自動取得
- **透明性**: すべての取引がブロックチェーンに記録
- **低手数料**: XRPLの低い取引手数料
- **高速**: 3-5秒で決済完了

#### 決済フロー

```
1. 商品を選択
2. 決済方法で「XRPL」を選択
3. XRP金額を確認（JPY換算）
4. XRPLアドレスを入力
5. エスクローが作成される
6. 商品受け取り後、支払いを解放
```

### 3. XRPLエスクロー

ブロックチェーン上でのスマートエスクロー機能。

#### エスクローの仕組み

- **作成**: 購入時にXRPLエスクローが作成される
- **ロック期間**: 7日間（設定可能）
- **解放**: 買い手が確認後、売り手に送金
- **キャンセル**: 30日以内にキャンセル可能

#### エスクローのメリット

- **信頼不要**: スマートコントラクトによる自動実行
- **透明性**: すべての取引がブロックチェーンで検証可能
- **安全性**: 秘密鍵は自分で管理

## セットアップ

### 1. XRPLアカウントの作成

#### テストネット（開発用）

```bash
# XRPLテストネットFaucetでアカウント作成
https://xrpl.org/xrp-testnet-faucet.html
```

#### メインネット（本番用）

XRPLウォレットアプリを使用：
- XUMM
- Ledger
- その他のXRPL対応ウォレット

### 2. 環境変数の設定

`.env`ファイルに以下を追加：

```env
# XRPL設定
XRPL_NETWORK=testnet  # または mainnet

# テストネット
XRPL_TESTNET_NODE=wss://s.altnet.rippletest.net:51233

# メインネット
XRPL_MAINNET_NODE=wss://xrplcluster.com

# プラットフォームウォレット
XRPL_PLATFORM_ADDRESS=rXXXXXXXXXXXXXXXXXXXXXXXXXXXX
XRPL_PLATFORM_SECRET=sXXXXXXXXXXXXXXXXXXXXXXXXXXXX

# エスクロー設定
XRPL_ESCROW_FINISH_AFTER_DAYS=7
XRPL_ESCROW_CANCEL_AFTER_DAYS=30

# 為替レート（動的取得も可能）
XRPL_XRP_TO_JPY_RATE=100
```

### 3. Composerパッケージのインストール

```bash
composer require hardcastle/xrpl-php
```

## API仕様

### XRPLログイン

#### チャレンジ生成

```http
POST /xrpl/challenge
Content-Type: application/json

{
  "xrpl_address": "rXXXXXXXXXXXXXXXXXXXXXXXXXXXX"
}
```

レスポンス：
```json
{
  "challenge": "abc123...",
  "message": "サイト売買プラットフォームへのログイン\nチャレンジ: abc123...\nタイムスタンプ: 2024-01-01T00:00:00Z"
}
```

#### 署名検証

```http
POST /xrpl/verify
Content-Type: application/json

{
  "xrpl_address": "rXXXXXXXXXXXXXXXXXXXXXXXXXXXX",
  "signature": "304402...",
  "public_key": "ED01FA..."
}
```

### XRPL決済

#### XRP価格取得

```http
GET /xrpl/price
```

レスポンス：
```json
{
  "rate": 100.50,
  "currency": "JPY"
}
```

#### XRPL決済実行

```http
POST /listings/{id}/payment/xrpl
Content-Type: application/json

{
  "plan_id": 1,
  "buyer_xrpl_address": "rXXXXXXXXXXXXXXXXXXXXXXXXXXXX"
}
```

## セキュリティ

### ベストプラクティス

1. **秘密鍵の管理**
   - 秘密鍵は絶対に共有しない
   - 環境変数で安全に管理
   - 本番環境では暗号化ストレージを使用

2. **署名検証**
   - すべての署名を厳密に検証
   - チャレンジの有効期限を設定（5分）
   - リプレイアタック対策

3. **エスクロー設定**
   - 適切なロック期間を設定
   - キャンセル期限を明確に
   - 手数料を考慮

## トラブルシューティング

### XRPLノードに接続できない

```
Error: Connection refused
```

**解決方法**:
1. ネットワーク設定を確認（testnet/mainnet）
2. ノードURLが正しいか確認
3. ファイアウォール設定を確認

### 署名検証に失敗する

```
Error: Signature verification failed
```

**解決方法**:
1. 公開鍵が正しいか確認
2. メッセージが改変されていないか確認
3. チャレンジの有効期限を確認

### エスクロー作成に失敗する

```
Error: Insufficient XRP balance
```

**解決方法**:
1. プラットフォームウォレットの残高を確認
2. XRPLの最小残高要件（10 XRP）を確認
3. トランザクション手数料を考慮

## XRPLリソース

- **公式ドキュメント**: https://xrpl.org/
- **開発者ポータル**: https://xrpl.org/docs.html
- **テストネットFaucet**: https://xrpl.org/xrp-testnet-faucet.html
- **エクスプローラー**: https://livenet.xrpl.org/
- **コミュニティ**: https://discord.gg/xrpl

## よくある質問

### Q: XRPLログインは安全ですか？

A: はい。秘密鍵を共有せず、署名ベースの認証を使用するため、非常に安全です。

### Q: XRPL決済の手数料は？

A: XRPLの取引手数料は非常に低く（約0.00001 XRP）、プラットフォーム手数料は10%です。

### Q: エスクローはいつ解放されますか？

A: 買い手が商品を確認後、手動で解放します。7日後に自動解放も可能です。

### Q: XRPの価格変動リスクは？

A: 決済時のレートで固定されますが、エスクロー期間中の価格変動リスクがあります。

### Q: テストネットで試せますか？

A: はい。`.env`で`XRPL_NETWORK=testnet`に設定してください。
