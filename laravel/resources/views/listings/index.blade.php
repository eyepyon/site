<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>出品一覧 - サイト売買プラットフォーム</title>
</head>
<body>
    <header>
        <h1>サイト売買プラットフォーム</h1>
        <nav>
            @auth
                <a href="{{ route('listings.create') }}">出品する</a>
                <a href="{{ route('transactions.index') }}">取引履歴</a>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit">ログアウト</button>
                </form>
            @else
                <a href="{{ route('login') }}">ログイン</a>
                <a href="{{ route('register') }}">会員登録（無料）</a>
            @endauth
        </nav>
    </header>

    @if(session('success'))
        <div>{{ session('success') }}</div>
    @endif

    <h2>出品一覧</h2>
    
    @if(!auth()->check())
        <p>※ 詳細を見るには<a href="{{ route('register') }}">会員登録（無料）</a>が必要です</p>
    @endif
    
    @foreach($listings as $listing)
    <div class="listing-card" style="border: 1px solid #ddd; padding: 15px; margin: 10px 0;">
        <h3>
            @if($listing->type === 'website')
                🌐
            @elseif($listing->type === 'app')
                📱
            @else
                ☁️
            @endif
            {{ $listing->title }}
        </h3>
        <p>{{ Str::limit($listing->description, 100) }}</p>
        <p><strong>価格:</strong> ¥{{ number_format($listing->price) }}〜</p>
        
        @if($listing->monthly_revenue)
        <p><strong>月間売上:</strong> ¥{{ number_format($listing->monthly_revenue) }}</p>
        @endif
        
        @if($listing->type === 'website' && $listing->monthly_pv)
        <p><strong>月間PV:</strong> {{ number_format($listing->monthly_pv) }}</p>
        @endif
        
        @if(in_array($listing->type, ['app', 'saas']) && $listing->total_users)
        <p><strong>登録ユーザー数:</strong> {{ number_format($listing->total_users) }}</p>
        @endif
        
        @auth
            <a href="{{ route('listings.show', $listing) }}">詳細を見る</a>
        @else
            <a href="{{ route('login') }}">ログインして詳細を見る</a>
        @endauth
    </div>
    @endforeach
    
    {{ $listings->links() }}
</body>
</html>
