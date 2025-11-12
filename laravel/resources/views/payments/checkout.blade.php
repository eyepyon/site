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

    <form id="payment-form" method="POST" action="{{ route('listings.payment', $listing) }}">
        @csrf
        <input type="hidden" name="plan_id" value="{{ $selectedPlan->id }}">
        <div id="card-element"></div>
        <input type="hidden" name="payment_method" id="payment-method">
        <button type="submit" style="background: #4CAF50; color: white; padding: 15px 30px; border: none; cursor: pointer; font-size: 16px;">
            ¥{{ number_format($selectedPlan->price) }} で購入する
        </button>
    </form>

    <script>
        const stripe = Stripe('{{ config("cashier.key") }}');
        const elements = stripe.elements();
        const cardElement = elements.create('card');
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
    </script>
</body>
</html>
