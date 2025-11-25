<?php

/**
 * 查詢財政部配號結果範例程式碼。
 *
 * 特店可透過 API 查詢財政部整合服務平台授權於綠界之發票號碼配號結果。
 *
 * 注意：如查無資料，可能的原因為取字軌號碼時並未授權於綠界，或字軌尚未取號完成。
 *
 * @see https://developers.ecpay.com.tw/?p=25206
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use CarlLee\EcPayB2B\EcPayClient;
use CarlLee\EcPayB2B\Queries\GetGovInvoiceWordSetting;
use CarlLee\EcPayB2B\Request;

// 測試環境設定
$server = 'https://einvoice-stage.ecpay.com.tw';
$merchantId = '2000132';
$hashKey = 'ejCk326UnaZWKisg';
$hashIV = 'q9jcZX8Ib9LM8wYk';

// 測試環境可關閉 SSL 驗證（正式環境請移除此行）
Request::setVerifySsl(false);

// 初始化 Client
$client = new EcPayClient($server, $hashKey, $hashIV);

// 建立查詢財政部配號結果請求
$query = new GetGovInvoiceWordSetting($merchantId, $hashKey, $hashIV);

// 設定查詢參數
$query->setInvoiceYear(date('Y'));  // 當年度（可傳入西元年，會自動轉換為民國年）

// 發送請求
try {
    $response = $client->send($query);
    $data = $response->getData();

    echo "=== 查詢財政部配號結果 ===\n";
    echo "RtnCode: " . ($data['RtnCode'] ?? 'N/A') . "\n";
    echo "RtnMsg: " . ($data['RtnMsg'] ?? 'N/A') . "\n";

    if ($response->success()) {
        echo "\n查詢成功！\n";

        if (isset($data['InvoiceInfo']) && is_array($data['InvoiceInfo'])) {
            echo "\n配號結果清單：\n";
            foreach ($data['InvoiceInfo'] as $index => $info) {
                echo "\n--- 配號 " . ($index + 1) . " ---\n";
                echo "發票期別: " . ($info['InvoiceTerm'] ?? 'N/A') . "\n";
                echo "字軌類別: " . ($info['InvType'] ?? 'N/A') . "\n";
                echo "發票字軌: " . ($info['InvoiceHeader'] ?? 'N/A') . "\n";
                echo "起始號碼: " . ($info['InvoiceStart'] ?? 'N/A') . "\n";
                echo "結束號碼: " . ($info['InvoiceEnd'] ?? 'N/A') . "\n";
                echo "申請本數: " . ($info['Number'] ?? 'N/A') . "\n";
            }
        } else {
            echo "查無配號資料\n";
            echo "可能原因：取字軌號碼時並未授權於綠界，或字軌尚未取號完成。\n";
        }
    } else {
        echo "\n查詢失敗\n";
    }

    echo "\n完整回應資料：\n";
    print_r($data);
} catch (Exception $e) {
    echo "錯誤: " . $e->getMessage() . "\n";
}

