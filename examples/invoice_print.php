<?php

/**
 * 發票列印範例程式碼。
 *
 * 特店可使用此 API 取得發票列印網址。
 *
 * @see https://developers.ecpay.com.tw/?p=14993
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use ecPay\eInvoiceB2B\EcPayClient;
use ecPay\eInvoiceB2B\Printing\InvoicePrint;
use ecPay\eInvoiceB2B\Request;

// 測試環境設定
$server = 'https://einvoice-stage.ecpay.com.tw';
$merchantId = '2000132';
$hashKey = 'ejCk326UnaZWKisg';
$hashIV = 'q9jcZX8Ib9LM8wYk';

// 測試環境可關閉 SSL 驗證（正式環境請移除此行）
Request::setVerifySsl(false);

// 初始化 Client
$client = new EcPayClient($server, $hashKey, $hashIV);

// 建立發票列印請求
$operation = new InvoicePrint($merchantId, $hashKey, $hashIV);

// 設定列印參數
$operation
    ->setInvoiceNumber('SA37758327')      // 發票號碼
    ->setInvoiceDate('2019-08-31');       // 發票開立日期

// 發送請求
try {
    $response = $client->send($operation);
    $data = $response->getData();

    echo "=== 發票列印結果 ===\n";
    echo "RtnCode: " . ($data['RtnCode'] ?? 'N/A') . "\n";
    echo "RtnMsg: " . ($data['RtnMsg'] ?? 'N/A') . "\n";

    if ($response->success()) {
        echo "\n取得成功！\n";

        // 取得列印網址
        if (isset($data['RtnData']['PrintUrl'])) {
            echo "發票列印網址: " . $data['RtnData']['PrintUrl'] . "\n";
        }
    } else {
        echo "\n取得失敗\n";
    }

    echo "\n完整回應資料：\n";
    print_r($data);
} catch (Exception $e) {
    echo "錯誤: " . $e->getMessage() . "\n";
}

