<?php

/**
 * 折讓發票確認範例程式碼。
 *
 * 特店(營業人)收到折讓發票訊息通知後，傳送折讓發票確認參數給綠界科技加值中心(以下簡稱綠界)，
 * 由綠界暫存相關資料。綠界會於隔日將折讓發票確認訊息後上傳至財政部電子發票整合服務平台。
 *
 * @see https://developers.ecpay.com.tw/?p=14880
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use CarlLee\EcPayB2B\EcPayClient;
use CarlLee\EcPayB2B\Operations\AllowanceConfirm;
use CarlLee\EcPayB2B\Parameter\ConfirmAction;
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

// 建立折讓發票確認請求
$operation = new AllowanceConfirm($merchantId, $hashKey, $hashIV);

// 設定確認參數
$operation
    ->setAllowanceNumber('SA377583270001')    // 折讓號碼（14碼）
    ->setAllowanceDate('2019-09-01')          // 折讓開立日期
    ->confirm();                               // 確認折讓

// 若要退回折讓，可使用：
// $operation->reject('退回折讓原因說明');

// 發送請求
try {
    $response = $client->send($operation);
    $data = $response->getData();

    echo "=== 折讓發票確認結果 ===\n";
    echo "RtnCode: " . ($data['RtnCode'] ?? 'N/A') . "\n";
    echo "RtnMsg: " . ($data['RtnMsg'] ?? 'N/A') . "\n";

    if ($response->success()) {
        echo "\n折讓確認成功！\n";
    } else {
        echo "\n折讓確認失敗\n";
    }

    echo "\n完整回應資料：\n";
    print_r($data);
} catch (Exception $e) {
    echo "錯誤: " . $e->getMessage() . "\n";
}

