@extends('layouts.app')

@section('title', 'ダッシュボード')

@section('content')
<h2 style="margin-bottom: 30px;">📊 ダッシュボード</h2>

<div class="stats">
    <div class="stat-card">
        <h3>総ユーザー数</h3>
        <div class="value">{{ number_format($stats['total_users']) }}</div>
    </div>
    <div class="stat-card">
        <h3>総出品数</h3>
        <div class="value">{{ number_format($stats['total_listings']) }}</div>
    </div>
    <div class="stat-card">
        <h3>公開中の出品</h3>
        <div class="value">{{ number_format($stats['active_listings']) }}</div>
    </div>
    <div class="stat-card">
        <h3>売却済み</h3>
        <div class="value">{{ number_format($stats['sold_listings']) }}</div>
    </div>
    <div class="stat-card">
        <h3>総取引数</h3>
        <div class="value">{{ number_format($stats['total_transactions']) }}</div>
    </div>
    <div class="stat-card">
        <h3>総売上</h3>
        <div class="value">¥{{ number_format($stats['total_revenue']) }}</div>
    </div>
    <div class="stat-card">
        <h3>プラットフォーム収益</h3>
        <div class="value">¥{{ number_format($stats['platform_revenue']) }}</div>
    </div>
</div>

<div class="card">
    <h3 style="margin-bottom: 20px;">決済方法別統計</h3>
    <table>
        <thead>
            <tr>
                <th>決済方法</th>
                <th>取引数</th>
                <th>合計金額</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payment_stats as $stat)
            <tr>
                <td>
                    @if($stat->payment_method === 'stripe')
                        💳 クレジットカード
                    @else
                        🌐 XRPL
                    @endif
                </td>
                <td>{{ number_format($stat->count) }}</td>
                <td>¥{{ number_format($stat->total) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="card">
    <h3 style="margin-bottom: 20px;">最近の取引</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>出品</th>
                <th>購入者</th>
                <th>販売者</th>
                <th>金額</th>
                <th>決済方法</th>
                <th>ステータス</th>
                <th>日時</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recent_transactions as $transaction)
            <tr>
                <td>{{ $transaction->id }}</td>
                <td>{{ $transaction->listing->title }}</td>
                <td>{{ $transaction->buyer->name }}</td>
                <td>{{ $transaction->seller->name }}</td>
                <td>¥{{ number_format($transaction->amount) }}</td>
                <td>
                    @if($transaction->payment_method === 'stripe')
                        💳
                    @else
                        🌐
                    @endif
                </td>
                <td>
                    <span class="badge badge-{{ $transaction->status === 'released' ? 'success' : 'warning' }}">
                        {{ $transaction->status }}
                    </span>
                </td>
                <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
