<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>取引詳細</title>
</head>
<body>
    <h1>取引詳細</h1>
    
    <a href="{{ route('transactions.index') }}">← 取引履歴に戻る</a>

    <div style="border: 1px solid #ddd; padding: 20px; margin: 20px 0;">
        <h2>{{ $transaction->listing->title }}</h2>
        
        @if($transaction->pricePlan)
        <div style="background: #f9f9f9; padding: 15px; margin: 10px 0;">
            <h3>購入プラン: {{ $transaction->pricePlan->name }}</h3>
            <p><strong>含まれる内容:</strong></p>
            <ul>
                @if($transaction->pricePlan->includes_members)
                <li>✅ 会員データ</li>
                @endif
                @if($transaction->pricePlan->includes_source)
                <li>✅ ソースコード</li>
                @endif
                @if($transaction->pricePlan->includes_installation)
                <li>✅ 設置サポート</li>
                @endif
            </ul>
        </div>
        @endif
        
        <p><strong>金額:</strong> ¥{{ number_format($transaction->amount) }}</p>
        <p><strong>手数料:</strong> ¥{{ number_format($transaction->platform_fee) }}</p>
        <p><strong>ステータス:</strong> {{ $transaction->status }}</p>
        <p><strong>購入日:</strong> {{ $transaction->paid_at?->format('Y年m月d日 H:i') }}</p>
        
        @if($transaction->status === 'escrowed' && $transaction->buyer_id === auth()->id())
        <form method="POST" action="{{ route('transactions.release', $transaction) }}">
            @csrf
            <button type="submit" style="background: #4CAF50; color: white; padding: 10px 20px; border: none; cursor: pointer;">
                支払いを解放する
            </button>
            <p style="font-size: 12px; color: #666;">※ 商品を受け取ったら支払いを解放してください</p>
        </form>
        @endif
    </div>
</body>
</html>
