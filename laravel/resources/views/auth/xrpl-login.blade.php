<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>XRPLログイン - サイト売買プラットフォーム</title>
    <style>
        .web3-login { max-width: 500px; margin: 50px auto; padding: 30px; border: 2px solid #23292E; border-radius: 10px; }
        .btn-xrpl { background: #23292E; color: white; padding: 15px 30px; border: none; cursor: pointer; width: 100%; font-size: 16px; border-radius: 5px; }
        .btn-xrpl:hover { background: #000; }
        .status { margin: 20px 0; padding: 15px; border-radius: 5px; }
        .status.info { background: #e3f2fd; color: #1976d2; }
        .status.error { background: #ffebee; color: #c62828; }
        .status.success { background: #e8f5e9; color: #2e7d32; }
    </style>
</head>
<body>
    <div class="web3-login">
        <h1>🌐 Web3ログイン</h1>
        <p>XRPLウォレットでログインします</p>

        <div id="status"></div>

        <div id="login-form">
            <div style="margin: 20px 0;">
                <label for="xrpl_address">XRPLアドレス</label>
                <input type="text" id="xrpl_address" placeholder="rXXXXXXXXXXXXXXXXXXXXXXXXXXXX" style="width: 100%; padding: 10px; margin-top: 5px;">
            </div>

            <button class="btn-xrpl" onclick="connectXRPL()">XRPLウォレットで接続</button>
        </div>

        <div id="signature-form" style="display: none;">
            <p>以下のメッセージに署名してください：</p>
            <textarea id="challenge-message" readonly style="width: 100%; height: 100px; margin: 10px 0;"></textarea>
            
            <div style="margin: 20px 0;">
                <label for="signature">署名</label>
                <input type="text" id="signature" placeholder="署名を入力" style="width: 100%; padding: 10px; margin-top: 5px;">
            </div>

            <div style="margin: 20px 0;">
                <label for="public_key">公開鍵</label>
                <input type="text" id="public_key" placeholder="公開鍵を入力" style="width: 100%; padding: 10px; margin-top: 5px;">
            </div>

            <button class="btn-xrpl" onclick="verifyAndLogin()">署名を検証してログイン</button>
        </div>

        <p style="margin-top: 20px; text-align: center;">
            または<a href="{{ route('login') }}">通常のログイン</a>
        </p>
    </div>

    <script>
        let currentAddress = '';
        let challengeData = null;

        function showStatus(message, type = 'info') {
            const statusDiv = document.getElementById('status');
            statusDiv.className = 'status ' + type;
            statusDiv.textContent = message;
            statusDiv.style.display = 'block';
        }

        async function connectXRPL() {
            const address = document.getElementById('xrpl_address').value.trim();
            
            if (!address) {
                showStatus('XRPLアドレスを入力してください', 'error');
                return;
            }

            if (!address.startsWith('r')) {
                showStatus('無効なXRPLアドレスです', 'error');
                return;
            }

            currentAddress = address;
            showStatus('チャレンジを生成中...', 'info');

            try {
                const response = await fetch('{{ route("xrpl.challenge") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ xrpl_address: address }),
                });

                const data = await response.json();

                if (!response.ok) {
                    showStatus(data.error || 'エラーが発生しました', 'error');
                    return;
                }

                challengeData = data;
                document.getElementById('challenge-message').value = data.message;
                document.getElementById('login-form').style.display = 'none';
                document.getElementById('signature-form').style.display = 'block';
                showStatus('XRPLウォレットでメッセージに署名してください', 'info');

            } catch (error) {
                showStatus('接続エラー: ' + error.message, 'error');
            }
        }

        async function verifyAndLogin() {
            const signature = document.getElementById('signature').value.trim();
            const publicKey = document.getElementById('public_key').value.trim();

            if (!signature || !publicKey) {
                showStatus('署名と公開鍵を入力してください', 'error');
                return;
            }

            showStatus('署名を検証中...', 'info');

            try {
                const response = await fetch('{{ route("xrpl.verify") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        xrpl_address: currentAddress,
                        signature: signature,
                        public_key: publicKey,
                    }),
                });

                const data = await response.json();

                if (!response.ok) {
                    showStatus(data.error || 'エラーが発生しました', 'error');
                    return;
                }

                showStatus('ログイン成功！リダイレクト中...', 'success');
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1000);

            } catch (error) {
                showStatus('検証エラー: ' + error.message, 'error');
            }
        }
    </script>
</body>
</html>
