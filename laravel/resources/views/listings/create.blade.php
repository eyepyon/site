<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>出品する - サイト売買プラットフォーム</title>
    <style>
        .metrics-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
        #app-metrics { display: none; }
    </style>
</head>
<body>
    <h1>出品する</h1>

    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('listings.store') }}">
        @csrf
        
        <div>
            <label for="title">タイトル</label>
            <input type="text" id="title" name="title" value="{{ old('title') }}" required>
        </div>

        <div>
            <label for="type">種類</label>
            <select id="type" name="type" required onchange="toggleMetrics()">
                <option value="website" {{ old('type') === 'website' ? 'selected' : '' }}>Webサイト</option>
                <option value="app" {{ old('type') === 'app' ? 'selected' : '' }}>アプリ</option>
                <option value="saas" {{ old('type') === 'saas' ? 'selected' : '' }}>SaaS</option>
            </select>
        </div>

        <div class="metrics-section">
            <h3>価格プラン</h3>
            <p>複数の価格プランを設定できます（最低1つ必要）</p>
            
            <div id="price-plans">
                <div class="price-plan-item" style="border: 1px solid #ccc; padding: 10px; margin: 10px 0;">
                    <h4>プラン 1</h4>
                    
                    <div>
                        <label>プラン名</label>
                        <input type="text" name="plans[0][name]" placeholder="例: ソースコードのみ" required>
                    </div>
                    
                    <div>
                        <label>価格（円）</label>
                        <input type="number" name="plans[0][price]" required>
                    </div>
                    
                    <div>
                        <label>プラン説明</label>
                        <textarea name="plans[0][description]" rows="2" placeholder="このプランに含まれる内容を説明してください"></textarea>
                    </div>

        <div>
            <label for="description">説明</label>
            <textarea id="description" name="description" rows="5" required>{{ old('description') }}</textarea>
        </div>

        <div>
            <label for="url">URL（任意）</label>
            <input type="url" id="url" name="url" value="{{ old('url') }}">
        </div>

        <div class="metrics-section">
            <h3>収益指標</h3>
            
            <div>
                <label for="monthly_revenue">月間売上（円）</label>
                <input type="number" id="monthly_revenue" name="monthly_revenue" value="{{ old('monthly_revenue') }}">
            </div>

            <div>
                <label for="monthly_profit">月間利益（円）</label>
                <input type="number" id="monthly_profit" name="monthly_profit" value="{{ old('monthly_profit') }}">
            </div>
        </div>

        <div class="metrics-section" id="web-metrics">
            <h3>トラフィック指標（Webサイト向け）</h3>

            <div>
                <label for="monthly_pv">月間PV（ページビュー）</label>
                <input type="number" id="monthly_pv" name="monthly_pv" value="{{ old('monthly_pv') }}">
            </div>

            <div>
                <label for="monthly_uu">月間UU（ユニークユーザー）</label>
                <input type="number" id="monthly_uu" name="monthly_uu" value="{{ old('monthly_uu') }}">
            </div>
        </div>

        <div class="metrics-section" id="app-metrics">
            <h3>ユーザー指標（アプリ/SaaS向け）</h3>

            <div>
                <label for="total_users">登録ユーザー数</label>
                <input type="number" id="total_users" name="total_users" value="{{ old('total_users') }}">
            </div>

            <div>
                <label for="dau">DAU（デイリーアクティブユーザー）</label>
                <input type="number" id="dau" name="dau" value="{{ old('dau') }}">
            </div>

            <div>
                <label for="mau">MAU（マンスリーアクティブユーザー）</label>
                <input type="number" id="mau" name="mau" value="{{ old('mau') }}">
            </div>

            <div>
                <label for="total_downloads">累計ダウンロード数</label>
                <input type="number" id="total_downloads" name="total_downloads" value="{{ old('total_downloads') }}">
            </div>
        </div>

        <button type="submit">出品する</button>
    </form>

    <script>
        let planIndex = 1;
        
        function toggleMetrics() {
            const type = document.getElementById('type').value;
            const webMetrics = document.getElementById('web-metrics');
            const appMetrics = document.getElementById('app-metrics');
            
            if (type === 'website') {
                webMetrics.style.display = 'block';
                appMetrics.style.display = 'none';
            } else if (type === 'app') {
                webMetrics.style.display = 'none';
                appMetrics.style.display = 'block';
            } else if (type === 'saas') {
                webMetrics.style.display = 'block';
                appMetrics.style.display = 'block';
            }
        }
        
        function addPricePlan() {
            const container = document.getElementById('price-plans');
            const newPlan = `
                <div class="price-plan-item" data-index="${planIndex}">
                    <h4>プラン ${planIndex + 1}</h4>
                    <div>
                        <label>プラン名</label>
                        <input type="text" name="plans[${planIndex}][name]" placeholder="例：会員データ込み" required>
                    </div>
                    <div>
                        <label>価格（円）</label>
                        <input type="number" name="plans[${planIndex}][price]" required>
                    </div>
                    <div>
                        <label>説明</label>
                        <textarea name="plans[${planIndex}][description]" rows="2" placeholder="プランの詳細説明"></textarea>
                    </div>
                    <div>
                        <label><input type="checkbox" name="plans[${planIndex}][includes_members]" value="1"> 会員データ含む</label>
                        <label><input type="checkbox" name="plans[${planIndex}][includes_source]" value="1"> ソースコード含む</label>
                        <label><input type="checkbox" name="plans[${planIndex}][includes_installation]" value="1"> 設置サポート含む</label>
                    </div>
                    <button type="button" onclick="removePricePlan(this)">このプランを削除</button>
                    <hr>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', newPlan);
            planIndex++;
        }
        
        function removePricePlan(button) {
            button.closest('.price-plan-item').remove();
        }
        
        toggleMetrics();
    </script>
</body>
</html>
