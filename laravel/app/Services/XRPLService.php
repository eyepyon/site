<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class XRPLService
{
    private string $nodeUrl;
    private array $platformWallet;

    public function __construct()
    {
        $network = config('xrpl.network');
        $this->nodeUrl = config("xrpl.nodes.{$network}");
        $this->platformWallet = config('xrpl.platform_wallet');
    }

    /**
     * XRPLアドレスの検証
     */
    public function validateAddress(string $address): bool
    {
        // XRPLアドレスは'r'で始まり、25-35文字
        return preg_match('/^r[1-9A-HJ-NP-Za-km-z]{24,34}$/', $address) === 1;
    }

    /**
     * アカウント情報の取得
     */
    public function getAccountInfo(string $address): ?array
    {
        try {
            $response = $this->sendRequest('account_info', [
                'account' => $address,
                'ledger_index' => 'validated',
            ]);

            return $response['account_data'] ?? null;
        } catch (Exception $e) {
            Log::error('XRPL account_info error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * エスクローの作成
     */
    public function createEscrow(
        string $destination,
        float $amount,
        int $finishAfterSeconds,
        int $cancelAfterSeconds
    ): ?array {
        try {
            $finishAfter = time() + $finishAfterSeconds;
            $cancelAfter = time() + $cancelAfterSeconds;

            $transaction = [
                'TransactionType' => 'EscrowCreate',
                'Account' => $this->platformWallet['address'],
                'Destination' => $destination,
                'Amount' => (string)($amount * 1000000), // XRPをdropsに変換
                'FinishAfter' => $finishAfter,
                'CancelAfter' => $cancelAfter,
            ];

            return $this->submitTransaction($transaction);
        } catch (Exception $e) {
            Log::error('XRPL escrow creation error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * エスクローの完了
     */
    public function finishEscrow(string $owner, int $sequence): ?array
    {
        try {
            $transaction = [
                'TransactionType' => 'EscrowFinish',
                'Account' => $this->platformWallet['address'],
                'Owner' => $owner,
                'OfferSequence' => $sequence,
            ];

            return $this->submitTransaction($transaction);
        } catch (Exception $e) {
            Log::error('XRPL escrow finish error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * エスクローのキャンセル
     */
    public function cancelEscrow(string $owner, int $sequence): ?array
    {
        try {
            $transaction = [
                'TransactionType' => 'EscrowCancel',
                'Account' => $this->platformWallet['address'],
                'Owner' => $owner,
                'OfferSequence' => $sequence,
            ];

            return $this->submitTransaction($transaction);
        } catch (Exception $e) {
            Log::error('XRPL escrow cancel error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * XRP価格の取得（JPY換算）
     */
    public function getXRPPriceInJPY(): float
    {
        try {
            // CoinGecko APIを使用
            $response = Http::get('https://api.coingecko.com/api/v3/simple/price', [
                'ids' => 'ripple',
                'vs_currencies' => 'jpy',
            ]);

            if ($response->successful()) {
                return $response->json()['ripple']['jpy'] ?? config('xrpl.currency.xrp_to_jpy_rate');
            }
        } catch (Exception $e) {
            Log::warning('Failed to fetch XRP price: ' . $e->getMessage());
        }

        return config('xrpl.currency.xrp_to_jpy_rate');
    }

    /**
     * JPYをXRPに変換
     */
    public function convertJPYtoXRP(float $jpy): float
    {
        $rate = $this->getXRPPriceInJPY();
        return $jpy / $rate;
    }

    /**
     * XRPLへのリクエスト送信
     */
    private function sendRequest(string $method, array $params = []): array
    {
        $response = Http::post($this->nodeUrl, [
            'method' => $method,
            'params' => [$params],
        ]);

        if (!$response->successful()) {
            throw new Exception('XRPL request failed');
        }

        $data = $response->json();

        if (isset($data['result']['status']) && $data['result']['status'] === 'error') {
            throw new Exception($data['result']['error_message'] ?? 'Unknown error');
        }

        return $data['result'] ?? [];
    }

    /**
     * トランザクションの送信
     */
    private function submitTransaction(array $transaction): array
    {
        // 実際の実装では、トランザクションに署名して送信
        // ここでは簡略化
        return $this->sendRequest('submit', $transaction);
    }

    /**
     * 署名の検証（Web3ログイン用）
     */
    public function verifySignature(string $message, string $signature, string $publicKey): bool
    {
        // XRPL署名の検証ロジック
        // 実際の実装では、xrpl-phpライブラリを使用
        try {
            // 簡略化: 実際にはED25519またはsecp256k1署名を検証
            return !empty($signature) && !empty($publicKey);
        } catch (Exception $e) {
            Log::error('Signature verification error: ' . $e->getMessage());
            return false;
        }
    }
}
