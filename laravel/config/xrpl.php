<?php

return [
    /*
    |--------------------------------------------------------------------------
    | XRPL Network Configuration
    |--------------------------------------------------------------------------
    */
    'network' => env('XRPL_NETWORK', 'testnet'), // mainnet, testnet, devnet
    
    'nodes' => [
        'mainnet' => env('XRPL_MAINNET_NODE', 'wss://xrplcluster.com'),
        'testnet' => env('XRPL_TESTNET_NODE', 'wss://s.altnet.rippletest.net:51233'),
        'devnet' => env('XRPL_DEVNET_NODE', 'wss://s.devnet.rippletest.net:51233'),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Platform Wallet
    |--------------------------------------------------------------------------
    | プラットフォームのXRPLウォレット（エスクロー管理用）
    */
    'platform_wallet' => [
        'address' => env('XRPL_PLATFORM_ADDRESS'),
        'secret' => env('XRPL_PLATFORM_SECRET'),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Escrow Configuration
    |--------------------------------------------------------------------------
    */
    'escrow' => [
        'finish_after_days' => env('XRPL_ESCROW_FINISH_AFTER_DAYS', 7), // エスクロー完了可能日数
        'cancel_after_days' => env('XRPL_ESCROW_CANCEL_AFTER_DAYS', 30), // エスクローキャンセル可能日数
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Currency Configuration
    |--------------------------------------------------------------------------
    */
    'currency' => [
        'xrp_to_jpy_rate' => env('XRPL_XRP_TO_JPY_RATE', 100), // 1 XRP = 100 JPY（動的に取得推奨）
    ],
];
