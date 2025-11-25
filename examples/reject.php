<?php

/**
 * 退回發票範例程式碼。
 *
 * 交易雙方因發生銷貨退回或發票內容開立錯誤，由特店(營業人)傳送退回發票參數給綠界科技加值中心，
 * 由綠界暫存相關資料。綠界會於隔日將發票退回後上傳至財政部電子發票整合服務平台。
 *
 * @see https://developers.ecpay.com.tw/?p=14870
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use CarlLee\EcPayB2B\EcPayClient;
use CarlLee\EcPayB2B\Operations\Reject;
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

// 建立退回發票請求
$operation = new Reject($merchantId, $hashKey, $hashIV);

// 設定退回參數
$operation
    ->salesInvoice()                          // 銷項發票（或使用 purchaseInvoice() 退回進項發票）
    ->setInvoiceNumber('SA37758327')          // 發票號碼
    ->setInvoiceDate('2019-08-31')            // 發票開立日期
    ->setRejectReason('發票內容錯誤');         // 退回原因

// 發送請求
try {
    $response = $client->send($operation);
    $data = $response->getData();

    echo "=== 退回發票結果 ===\n";
    echo "RtnCode: " . ($data['RtnCode'] ?? 'N/A') . "\n";
    echo "RtnMsg: " . ($data['RtnMsg'] ?? 'N/A') . "\n";

    if ($response->success()) {
        echo "\n退回申請成功！等待交易相對人確認\n";
    } else {
        echo "\n退回申請失敗\n";
    }

    echo "\n完整回應資料：\n";
    print_r($data);
} catch (Exception $e) {
    echo "錯誤: " . $e->getMessage() . "\n";
}

