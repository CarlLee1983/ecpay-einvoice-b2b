<?php

/**
 * 查詢字軌範例程式碼。
 *
 * 特店系統可使用此 API 查詢字軌號碼以及字軌的使用情況。
 *
 * @see https://developers.ecpay.com.tw/?p=14845
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use ecPay\eInvoiceB2B\EcPayClient;
use ecPay\eInvoiceB2B\Parameter\InvoiceTerm;
use ecPay\eInvoiceB2B\Parameter\InvType;
use ecPay\eInvoiceB2B\Parameter\UseStatus;
use ecPay\eInvoiceB2B\Queries\GetInvoiceWordSetting;
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

// 建立查詢字軌請求
$query = new GetInvoiceWordSetting($merchantId, $hashKey, $hashIV);

// 設定查詢參數
$query
    ->setInvoiceYear(date('Y'))  // 當年度（可傳入西元年，會自動轉換為民國年）
    ->setInvoiceTerm(InvoiceTerm::ALL)  // 全部期別
    ->setUseStatus(UseStatus::ALL)  // 全部狀態
    ->setInvType(InvType::GENERAL);  // 一般稅額發票（可選）

// 發送請求
try {
    $response = $client->send($query);
    $data = $response->getData();

    echo "=== 查詢字軌結果 ===\n";
    echo "RtnCode: " . ($data['RtnCode'] ?? 'N/A') . "\n";
    echo "RtnMsg: " . ($data['RtnMsg'] ?? 'N/A') . "\n";

    if ($response->success()) {
        echo "\n成功！\n";

        if (isset($data['InvoiceInfo']) && is_array($data['InvoiceInfo'])) {
            echo "\n字軌資訊：\n";
            foreach ($data['InvoiceInfo'] as $index => $info) {
                echo "\n--- 字軌 " . ($index + 1) . " ---\n";
                echo "TrackID: " . ($info['TrackID'] ?? 'N/A') . "\n";
                echo "發票年度: " . ($info['InvoiceYear'] ?? 'N/A') . "\n";
                echo "發票期別: " . ($info['InvoiceTerm'] ?? 'N/A') . "\n";
                echo "字軌名稱: " . ($info['InvoiceHeader'] ?? 'N/A') . "\n";
                echo "起始號碼: " . ($info['InvoiceStart'] ?? 'N/A') . "\n";
                echo "結束號碼: " . ($info['InvoiceEnd'] ?? 'N/A') . "\n";
                echo "已使用號碼: " . ($info['InvoiceNo'] ?? 'N/A') . "\n";
                echo "使用狀態: " . ($info['UseStatus'] ?? 'N/A') . "\n";
                echo "最後開立時間: " . ($info['InvoiceLastDate'] ?? 'N/A') . "\n";
            }
        } else {
            echo "查無字軌資訊\n";
        }
    } else {
        echo "\n查詢失敗\n";
    }

    echo "\n完整回應資料：\n";
    print_r($data);
} catch (Exception $e) {
    echo "錯誤: " . $e->getMessage() . "\n";
}
