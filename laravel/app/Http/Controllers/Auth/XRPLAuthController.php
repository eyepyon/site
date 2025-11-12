<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\XRPLService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class XRPLAuthController extends Controller
{
    public function __construct(
        private XRPLService $xrplService
    ) {}

    /**
     * XRPLログインページを表示
     */
    public function showLoginForm()
    {
        return view('auth.xrpl-login');
    }

    /**
     * ログインチャレンジの生成
     */
    public function generateChallenge(Request $request)
    {
        $request->validate([
            'xrpl_address' => 'required|string',
        ]);

        $address = $request->xrpl_address;

        if (!$this->xrplService->validateAddress($address)) {
            return response()->json([
                'error' => '無効なXRPLアドレスです',
            ], 400);
        }

        // チャレンジメッセージを生成
        $challenge = Str::random(32);
        $message = "サイト売買プラットフォームへのログイン\nチャレンジ: {$challenge}\nタイムスタンプ: " . now()->toIso8601String();

        // セッションに保存
        session([
            'xrpl_challenge' => $challenge,
            'xrpl_address' => $address,
            'xrpl_challenge_expires' => now()->addMinutes(5),
        ]);

        return response()->json([
            'challenge' => $challenge,
            'message' => $message,
        ]);
    }

    /**
     * XRPL署名を検証してログイン
     */
    public function verifyAndLogin(Request $request)
    {
        $request->validate([
            'xrpl_address' => 'required|string',
            'signature' => 'required|string',
            'public_key' => 'required|string',
        ]);

        // チャレンジの検証
        $storedChallenge = session('xrpl_challenge');
        $storedAddress = session('xrpl_address');
        $expiresAt = session('xrpl_challenge_expires');

        if (!$storedChallenge || !$storedAddress || now()->isAfter($expiresAt)) {
            return response()->json([
                'error' => 'チャレンジが無効または期限切れです',
            ], 400);
        }

        if ($storedAddress !== $request->xrpl_address) {
            return response()->json([
                'error' => 'アドレスが一致しません',
            ], 400);
        }

        // 署名の検証
        $message = "サイト売買プラットフォームへのログイン\nチャレンジ: {$storedChallenge}\nタイムスタンプ: " . session('xrpl_challenge_timestamp');

        if (!$this->xrplService->verifySignature($message, $request->signature, $request->public_key)) {
            return response()->json([
                'error' => '署名の検証に失敗しました',
            ], 400);
        }

        // ユーザーを取得または作成
        $user = User::firstOrCreate(
            ['xrpl_address' => $request->xrpl_address],
            [
                'name' => 'XRPL User ' . substr($request->xrpl_address, 0, 8),
                'email' => $request->xrpl_address . '@xrpl.local',
                'password' => bcrypt(Str::random(32)), // ランダムパスワード
            ]
        );

        // ログイン
        Auth::login($user);

        // セッションをクリア
        session()->forget(['xrpl_challenge', 'xrpl_address', 'xrpl_challenge_expires']);

        return response()->json([
            'success' => true,
            'redirect' => route('home'),
        ]);
    }

    /**
     * XRPLアカウント情報の取得
     */
    public function getAccountInfo(Request $request)
    {
        $request->validate([
            'xrpl_address' => 'required|string',
        ]);

        $accountInfo = $this->xrplService->getAccountInfo($request->xrpl_address);

        if (!$accountInfo) {
            return response()->json([
                'error' => 'アカウント情報を取得できませんでした',
            ], 404);
        }

        return response()->json([
            'address' => $accountInfo['Account'],
            'balance' => ($accountInfo['Balance'] ?? 0) / 1000000, // dropsをXRPに変換
        ]);
    }
}
