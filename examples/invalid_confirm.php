<?php

/**
 * 作廢發票確認範例程式碼。
 *
 * 特店(營業人)收到作廢發票訊息通知後，傳送作廢發票確認參數給綠界科技加值中心(以下簡稱綠界)，
 * 由綠界暫存相關資料。綠界會於隔日將作廢發票確認訊息後上傳至財政部電子發票整合服務平台。
 *
 * @see https://developers.ecpay.com.tw/?p=14865
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use CarlLee\EcPayB2B\EcPayClient;
use CarlLee\EcPayB2B\Operations\InvalidConfirm;
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

// 建立作廢發票確認請求
$operation = new InvalidConfirm($merchantId, $hashKey, $hashIV);

// 設定確認參數
$operation
    ->setInvoiceNumber('SA37758327')      // 發票號碼
    ->setInvoiceDate('2019-08-31')        // 發票開立日期
    ->confirm();                           // 確認作廢

// 若要拒絕作廢，可使用：
// $operation->reject('拒絕作廢原因說明');

// 發送請求
try {
    $response = $client->send($operation);
    $data = $response->getData();

    echo "=== 作廢發票確認結果 ===\n";
    echo "RtnCode: " . ($data['RtnCode'] ?? 'N/A') . "\n";
    echo "RtnMsg: " . ($data['RtnMsg'] ?? 'N/A') . "\n";

    if ($response->success()) {
        echo "\n作廢確認成功！\n";
    } else {
        echo "\n作廢確認失敗\n";
    }

    echo "\n完整回應資料：\n";
    print_r($data);
} catch (Exception $e) {
    echo "錯誤: " . $e->getMessage() . "\n";
}

