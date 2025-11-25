<?php

/**
 * 字軌與配號設定範例程式碼。
 *
 * 當營業人(特店)取得財政部的配號結果後，可建立當年度(含當月)或下個年度的字軌。
 * 在開立發票之前，必須先設定字軌區間，並且可設定多組。
 *
 * 注意：在新增字軌前須自行檢核字軌正確性。
 * 注意：新增字軌後，字軌狀態預設為已審核通過但未啟用，請使用設定字軌號碼狀態 API 進行啟用。
 *
 * @see https://developers.ecpay.com.tw/?p=14835
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use ecPay\eInvoiceB2B\EcPayClient;
use ecPay\eInvoiceB2B\Operations\AddInvoiceWordSetting;
use ecPay\eInvoiceB2B\Parameter\InvoiceTerm;
use ecPay\eInvoiceB2B\Parameter\InvType;
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

// 建立字軌設定請求
$operation = new AddInvoiceWordSetting($merchantId, $hashKey, $hashIV);

// 設定參數
$operation
    ->setInvoiceYear(date('Y'))           // 發票年度（可傳入西元年，會自動轉換為民國年）
    ->setInvoiceTerm(InvoiceTerm::JAN_FEB) // 發票期別（1: 1-2月）
    ->setInvType(InvType::GENERAL)         // 字軌類別（07: 一般稅額發票）
    ->setInvoiceHeader('TW')               // 發票字軌（兩碼英文）
    ->setInvoiceRange('10000000', '10000049'); // 發票號碼區間

// 也可以分開設定起始和結束號碼：
// ->setInvoiceStart('10000000')
// ->setInvoiceEnd('10000049')

// 發送請求
try {
    $response = $client->send($operation);
    $data = $response->getData();

    echo "=== 字軌與配號設定結果 ===\n";
    echo "RtnCode: " . ($data['RtnCode'] ?? 'N/A') . "\n";
    echo "RtnMsg: " . ($data['RtnMsg'] ?? 'N/A') . "\n";

    if ($response->success()) {
        echo "\n字軌新增成功！\n";
        echo "TrackID: " . ($data['TrackID'] ?? 'N/A') . "\n";
        echo "\n請留存 TrackID 作為設定字軌號碼啟用狀態用。\n";
        echo "新增字軌後，字軌狀態預設為已審核通過但未啟用，\n";
        echo "請使用設定字軌號碼狀態 API 進行啟用。\n";
    } else {
        echo "\n字軌新增失敗\n";
    }

    echo "\n完整回應資料：\n";
    print_r($data);
} catch (Exception $e) {
    echo "錯誤: " . $e->getMessage() . "\n";
}

