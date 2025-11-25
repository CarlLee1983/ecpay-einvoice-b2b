<?php

declare(strict_types=1);

/**
 * 綠界 B2B 電子發票 API 設定檔（交換模式）。
 *
 * 介接注意事項：
 * - 僅支援 HTTPS (443 port) 連線
 * - 支援 TLS 1.1 以上加密通訊協定
 * - 使用 HTTP POST 方式傳送
 *
 * @see https://developers.ecpay.com.tw/?p=14825
 */

return [
    /*
    |--------------------------------------------------------------------------
    | ECPay 介接環境
    |--------------------------------------------------------------------------
    |
    | 預設為綠界提供的測試環境。正式環境請改用
    | https://einvoice.ecpay.com.tw
    |
    | 測試環境：https://einvoice-stage.ecpay.com.tw (TCP 443)
    | 正式環境：https://einvoice.ecpay.com.tw (TCP 443)
    |
    */
    'server' => env('ECPAY_EINVOICE_B2B_SERVER', 'https://einvoice-stage.ecpay.com.tw'),

    /*
    |--------------------------------------------------------------------------
    | 商店憑證設定
    |--------------------------------------------------------------------------
    |
    | MerchantID/HashKey/HashIV 為綠界提供的專屬金鑰。建議透過
    | .env 檔配置，避免直接寫在程式碼中。
    |
    | ⚠️ 安全提醒：請勿將金鑰資訊存放或顯示於前端網頁內（如 JavaScript、HTML、CSS 等），
    |    避免金鑰被盜取使用造成損失及交易資料外洩。
    |
    */
    'merchant_id' => env('ECPAY_EINVOICE_B2B_MERCHANT_ID', ''),
    'hash_key' => env('ECPAY_EINVOICE_B2B_HASH_KEY', ''),
    'hash_iv' => env('ECPAY_EINVOICE_B2B_HASH_IV', ''),

    /*
    |--------------------------------------------------------------------------
    | SSL 驗證設定
    |--------------------------------------------------------------------------
    |
    | 正式環境建議啟用 SSL 驗證（true）。
    | 測試環境如遇到憑證問題，可暫時關閉（false）。
    |
    */
    'verify_ssl' => env('ECPAY_EINVOICE_B2B_VERIFY_SSL', true),

    /*
    |--------------------------------------------------------------------------
    | 工廠額外設定
    |--------------------------------------------------------------------------
    |
    | aliases: 自訂別名 => 類別對應，例如 'custom.invoice' => App\Invoice\CustomInvoice::class
    | initializers: 需要傳入可呼叫物件（建議使用 __invoke 類別），用來統一設定
    |               RelateNumber、預設欄位等邏輯。
    |
    */
    'factory' => [
        'aliases' => [
            // 'custom.invoice' => \App\Invoices\CustomInvoice::class,
        ],
        'initializers' => [
            // \App\Invoices\Initializers\DefaultRelateNumber::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 便利綁定
    |--------------------------------------------------------------------------
    |
    | 可經由 app('ecpay-b2b.invoice') 等方式解析對應操作物件。
    | Facade 也會依照這裡的 key 對應，例如 EcPayInvoice::invoice()。
    |
    | 注意：B2B 操作類別需待實作後再開啟對應的綁定。
    |
    */
    'bindings' => [
        // 'invoice' => 'invoice',
        // 'allowance' => 'operations.allowance_invoice',
        // 'invalid_invoice' => 'operations.invalid_invoice',
        // 'query_invoice' => 'queries.get_invoice',
    ],
];
