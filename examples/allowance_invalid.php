<?php

/**
 * 作廢折讓發票範例程式碼。
 *
 * 交易雙方因發生折讓內容開立錯誤，由特店(營業人)傳送作廢折讓發票參數給綠界科技加值中心，
 * 由綠界暫存相關資料。綠界會於隔日將折讓作廢後上傳至財政部電子發票整合服務平台。
 *
 * @see https://developers.ecpay.com.tw/?p=14889
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use CarlLee\EcPayB2B\EcPayClient;
use CarlLee\EcPayB2B\Operations\AllowanceInvalid;
use CarlLee\EcPayB2B\Parameter\InvalidReason;
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

// 建立作廢折讓發票請求
$operation = new AllowanceInvalid($merchantId, $hashKey, $hashIV);

// 設定作廢參數
$operation
    ->salesInvoice()                              // 銷項折讓（或使用 purchaseInvoice() 作廢進項折讓）
    ->setAllowanceNumber('SA377583270001')        // 折讓號碼（14碼）
    ->setAllowanceDate('2019-09-01')              // 折讓開立日期
    ->setInvalidReason(InvalidReason::ALLOWANCE_ERROR);  // 作廢原因

// 或使用自訂作廢原因：
// $operation->setInvalidReason('折讓金額計算錯誤');

// 發送請求
try {
    $response = $client->send($operation);
    $data = $response->getData();

    echo "=== 作廢折讓發票結果 ===\n";
    echo "RtnCode: " . ($data['RtnCode'] ?? 'N/A') . "\n";
    echo "RtnMsg: " . ($data['RtnMsg'] ?? 'N/A') . "\n";

    if ($response->success()) {
        echo "\n作廢折讓申請成功！等待交易相對人確認\n";
    } else {
        echo "\n作廢折讓申請失敗\n";
    }

    echo "\n完整回應資料：\n";
    print_r($data);
} catch (Exception $e) {
    echo "錯誤: " . $e->getMessage() . "\n";
}

