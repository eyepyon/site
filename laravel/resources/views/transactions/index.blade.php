<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>取引履歴</title>
</head>
<body>
    <h1>取引履歴</h1>
    
    <a href="{{ route('home') }}">← トップに戻る</a>

    @if($transactions->isEmpty())
        <p>取引履歴がありません</p>
    @else
        @foreach($transactions as $transaction)
        <div style="border: 1px solid #ddd; padding: 15px; margin: 10px 0;">
            <h3>{{ $transaction->listing->title }}</h3>
            @if($transaction->pricePlan)
            <p><strong>プラン:</strong> {{ $transaction->pricePlan->name }}</p>
            @endif
            <p><strong>金額:</strong> ¥{{ number_format($transaction->amount) }}</p>
            <p><strong>ステータス:</strong> {{ $transaction->status }}</p>
            <p><strong>購入日:</strong> {{ $transaction->paid_at?->format('Y年m月d日') }}</p>
            <a href="{{ route('transactions.show', $transaction) }}">詳細を見る</a>
        </div>
        @endforeach
        
        {{ $transactions->links() }}
    @endif
</body>
</html>
