<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>購入手続き</title>
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
    <h1>購入手続き</h1>
    
    <div style="border: 1px solid #ddd; padding: 20px; margin: 20px 0;">
        <h2>{{ $listing->title }}</h2>
        <h3>選択プラン: {{ $selectedPlan->name }}</h3>
        <p><strong>価格: ¥{{ number_format($selectedPlan->price) }}</strong></p>
        
        @if($selectedPlan->description)
        <p>{{ $selectedPlan->description }}</p>
        @endif
        
        <div>
            <strong>含まれる内容:</strong>
            <ul>
                @if($selectedPlan->includes_members)
                <li>✅ 会員データ</li>
                @endif
                @if($selectedPlan->includes_source)
                <li>✅ ソースコード</li>
                @endif
                @if($selectedPlan->includes_installation)
                <li>✅ 設置サポート</li>
                @endif
            </ul>
        </div>
    </div>

    <div style="margin: 30px 0;">
        <h3>決済方法を選択</h3>
        <div style="display: flex; gap: 20px; margin: 20px 0;">
            <button onclick="selectPaymentMethod('stripe')" id="btn-stripe" style="flex: 1; padding: 15px; background: #635BFF; color: white; border: none; cursor: pointer; border-radius: 5px;">
                💳 クレジットカード（Stripe）
            </button>
            <button onclick="selectPaymentMethod('xrpl')" id="btn-xrpl" style="flex: 1; padding: 15px; background: #23292E; color: white; border: none; cursor: pointer; border-radius: 5px;">
                🌐 XRPL（暗号資産）
            </button>
        </div>
    </div>

    <!-- Stripe決済フォーム -->
    <div id="stripe-payment" style="display: none;">
        <h3>クレジットカード決済</h3>
        <form id="payment-form" method="POST" action="{{ route('listings.payment', $listing) }}">
            @csrf
            <input type="hidden" name="plan_id" value="{{ $selectedPlan->id }}">
            <input type="hidden" name="payment_type" value="stripe">
            <div id="card-element" style="padding: 15px; border: 1px solid #ddd; border-radius: 5px; margin: 20px 0;"></div>
            <input type="hidden" name="payment_method" id="payment-method">
            <button type="submit" style="background: #4CAF50; color: white; padding: 15px 30px; border: none; cursor: pointer; font-size: 16px; width: 100%;">
                ¥{{ number_format($selectedPlan->price) }} で購入する
            </button>
        </form>
    </div>

    <!-- XRPL決済フォーム -->
    <div id="xrpl-payment" style="display: none;">
        <h3>XRPL決済</h3>
        <div style="background: #f9f9f9; padding: 20px; border-radius: 5px; margin: 20px 0;">
            <p><strong>支払い金額:</strong></p>
            <p style="font-size: 24px; margin: 10px 0;">
                <span id="xrp-amount">計算中...</span> XRP
            </p>
            <p style="font-size: 14px; color: #666;">
                ≈ ¥{{ number_format($selectedPlan->price) }} (レート: <span id="xrp-rate">取得中...</span> JPY/XRP)
            </p>
        </div>

        <form id="xrpl-payment-form" method="POST" action="{{ route('listings.payment.xrpl', $listing) }}">
            @csrf
            <input type="hidden" name="plan_id" value="{{ $selectedPlan->id }}">
            <input type="hidden" name="payment_type" value="xrpl">
            
            <div style="margin: 20px 0;">
                <label for="buyer_xrpl_address">あなたのXRPLアドレス</label>
                <input type="text" id="buyer_xrpl_address" name="buyer_xrpl_address" 
                       value="{{ auth()->user()->xrpl_address ?? '' }}"
                       placeholder="rXXXXXXXXXXXXXXXXXXXXXXXXXXXX" 
                       style="width: 100%; padding: 10px; margin-top: 5px;" required>
            </div>

            <div style="background: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0;">
                <p><strong>⚠️ 重要な注意事項:</strong></p>
                <ol style="margin: 10px 0; padding-left: 20px;">
                    <li>XRPLエスクローが作成されます</li>
                    <li>商品確認後、7日以内に支払いが解放されます</li>
                    <li>30日以内にキャンセル可能です</li>
                    <li>プラットフォーム手数料10%が含まれます</li>
                </ol>
            </div>

            <button type="submit" style="background: #23292E; color: white; padding: 15px 30px; border: none; cursor: pointer; font-size: 16px; width: 100%;">
                XRPLエスクローで購入する
            </button>
        </form>
    </div>

    <script>
        let stripe, elements, cardElement;
        let currentPaymentMethod = null;

        function selectPaymentMethod(method) {
            currentPaymentMethod = method;
            
            // ボタンのスタイル更新
            document.getElementById('btn-stripe').style.opacity = method === 'stripe' ? '1' : '0.5';
            document.getElementById('btn-xrpl').style.opacity = method === 'xrpl' ? '1' : '0.5';
            
            // フォームの表示切り替え
            document.getElementById('stripe-payment').style.display = method === 'stripe' ? 'block' : 'none';
            document.getElementById('xrpl-payment').style.display = method === 'xrpl' ? 'block' : 'none';
            
            if (method === 'stripe' && !stripe) {
                initStripe();
            } else if (method === 'xrpl') {
                fetchXRPPrice();
            }
        }

        function initStripe() {
            stripe = Stripe('{{ config("cashier.key") }}');
            elements = stripe.elements();
            cardElement = elements.create('card');
            cardElement.mount('#card-element');

            const form = document.getElementById('payment-form');
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                const { setupIntent } = await fetch('{{ route("setup-intent") }}')
                    .then(r => r.json());
                
                const { error, paymentMethod } = await stripe.confirmCardSetup(
                    setupIntent.client_secret,
                    { payment_method: { card: cardElement } }
                );

                if (error) {
                    alert(error.message);
                } else {
                    document.getElementById('payment-method').value = paymentMethod.id;
                    form.submit();
                }
            });
        }

        async function fetchXRPPrice() {
            try {
                const response = await fetch('{{ route("xrpl.price") }}');
                const data = await response.json();
                
                const jpy = {{ $selectedPlan->price }};
                const xrpAmount = (jpy / data.rate).toFixed(6);
                
                document.getElementById('xrp-amount').textContent = xrpAmount;
                document.getElementById('xrp-rate').textContent = data.rate.toFixed(2);
            } catch (error) {
                console.error('XRP価格取得エラー:', error);
                document.getElementById('xrp-amount').textContent = 'エラー';
                document.getElementById('xrp-rate').textContent = 'エラー';
            }
        }

        // デフォルトでStripeを選択
        selectPaymentMethod('stripe');
    </script>
</body>
</html>
