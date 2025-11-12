<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $listing->title }} - サイト売買プラットフォーム</title>
</head>
<body>
    <a href="{{ route('home') }}">← 一覧に戻る</a>

    <h1>{{ $listing->title }}</h1>

    <div style="border: 2px solid #4CAF50; padding: 20px; margin: 20px 0; background: #f9f9f9;">
        <h2>💰 価格プラン</h2>
        
        @foreach($listing->pricePlans as $plan)
        <div style="border: 1px solid #ddd; padding: 15px; margin: 10px 0; background: white;">
            <h3>{{ $plan->name }} - ¥{{ number_format($plan->price) }}</h3>
            
            @if($plan->description)
            <p>{{ $plan->description }}</p>
            @endif
            
            <div style="margin-top: 10px;">
                <strong>含まれる内容:</strong>
                <ul>
                    @if($plan->includes_members)
                    <li>✅ 会員データ</li>
                    @endif
                    @if($plan->includes_source)
                    <li>✅ ソースコード</li>
                    @endif
                    @if($plan->includes_installation)
                    <li>✅ 設置サポート</li>
                    @endif
                    @if(!$plan->includes_members && !$plan->includes_source && !$plan->includes_installation)
                    <li>基本パッケージ</li>
                    @endif
                </ul>
            </div>
            
            @if($listing->status === 'active' && auth()->id() !== $listing->seller_id)
            <form method="GET" action="{{ route('listings.checkout', $listing) }}" style="margin-top: 10px;">
                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                <button type="submit" style="background: #4CAF50; color: white; padding: 10px 20px; border: none; cursor: pointer;">
                    このプランで購入
                </button>
            </form>
            @endif
        </div>
        @endforeach
    </div>

    <div>
        <h2>種類</h2>
        <p>
            @if($listing->type === 'website')
                🌐 Webサイト
            @elseif($listing->type === 'app')
                📱 アプリ
            @else
                ☁️ SaaS
            @endif
        </p>
    </div>

    <div>
        <h2>説明</h2>
        <p>{{ $listing->description }}</p>
    </div>

    @if($listing->url)
    <div>
        <h2>URL</h2>
        <p><a href="{{ $listing->url }}" target="_blank">{{ $listing->url }}</a></p>
    </div>
    @endif

    @if($listing->monthly_revenue || $listing->monthly_profit)
    <div style="border: 1px solid #ddd; padding: 15px; margin: 20px 0;">
        <h2>💰 収益指標</h2>
        
        @if($listing->monthly_revenue)
        <p><strong>月間売上:</strong> ¥{{ number_format($listing->monthly_revenue) }}</p>
        @endif

        @if($listing->monthly_profit)
        <p><strong>月間利益:</strong> ¥{{ number_format($listing->monthly_profit) }}</p>
        @endif
    </div>
    @endif

    @if($listing->type === 'website' && ($listing->monthly_pv || $listing->monthly_uu))
    <div style="border: 1px solid #ddd; padding: 15px; margin: 20px 0;">
        <h2>📊 トラフィック指標</h2>
        
        @if($listing->monthly_pv)
        <p><strong>月間PV:</strong> {{ number_format($listing->monthly_pv) }}</p>
        @endif

        @if($listing->monthly_uu)
        <p><strong>月間UU:</strong> {{ number_format($listing->monthly_uu) }}</p>
        @endif
    </div>
    @endif

    @if(in_array($listing->type, ['app', 'saas']) && ($listing->total_users || $listing->dau || $listing->mau || $listing->total_downloads))
    <div style="border: 1px solid #ddd; padding: 15px; margin: 20px 0;">
        <h2>👥 ユーザー指標</h2>
        
        @if($listing->total_users)
        <p><strong>登録ユーザー数:</strong> {{ number_format($listing->total_users) }}</p>
        @endif

        @if($listing->dau)
        <p><strong>DAU:</strong> {{ number_format($listing->dau) }}</p>
        @endif

        @if($listing->mau)
        <p><strong>MAU:</strong> {{ number_format($listing->mau) }}</p>
        @endif

        @if($listing->total_downloads)
        <p><strong>累計ダウンロード数:</strong> {{ number_format($listing->total_downloads) }}</p>
        @endif
    </div>
    @endif

    <div>
        <h2>販売者</h2>
        <p>{{ $listing->seller->name }}</p>
    </div>

    @if($listing->status === 'sold')
        <p style="color: red; font-weight: bold;">この出品は売却済みです</p>
    @elseif(auth()->id() === $listing->seller_id)
        <a href="{{ route('listings.edit', $listing) }}" style="display: inline-block; padding: 10px 20px; background: #2196F3; color: white; text-decoration: none;">編集する</a>
    @endif
</body>
</html>
