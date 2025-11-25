<?php

/**
 * 發送發票通知範例程式碼。
 *
 * B2B電子發票應在任何發票狀態變動時通知交易雙方，特店(營業人)可使用此API來發送電子發票通知，
 * 綠界將以發票開立時所提供之交易雙方聯絡資料進行通知。
 *
 * 注意：測試環境下綠界不會『主動』發送任何通知，需於廠商管理後台使用『補發通知』，
 * 才會寄送通知信到指定信箱。
 *
 * @see https://developers.ecpay.com.tw/?p=14988
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use CarlLee\EcPayB2B\EcPayClient;
use CarlLee\EcPayB2B\Notifications\Notify;
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

// 建立發送發票通知請求
$operation = new Notify($merchantId, $hashKey, $hashIV);

// 設定通知參數
$operation
    ->setInvoiceDate('2019-08-31')                      // 發票開立日期
    ->setInvoiceNumber('SA37758327')                    // 發票號碼
    ->setNotifyMail('test@example.com')                 // 發送電子郵件
    ->issueNotify()                                     // 發票開立通知
    ->notifyAll();                                      // 皆發送（客戶與特店）

// 也可以發送多個 Email：
// $operation->setNotifyMails(['buyer@example.com', 'seller@example.com']);

// 或使用常數設定：
// $operation->setInvoiceTag(InvoiceTag::ISSUE);
// $operation->setNotified(NotifyTarget::ALL);

// 其他通知類型範例：
// $operation->invalidNotify();                         // 發票作廢通知
// $operation->rejectNotify();                          // 發票退回通知
// $operation->allowanceNotify('AA123456780001AB');     // 開立折讓通知（帶折讓單編號）
// $operation->allowanceInvalidNotify('AA123456780001AB'); // 作廢折讓通知
// $operation->issueConfirmNotify();                    // 開立發票確認通知
// $operation->invalidConfirmNotify();                  // 作廢發票確認通知
// $operation->rejectConfirmNotify();                   // 退回發票確認通知
// $operation->allowanceConfirmNotify('AA123456780001AB'); // 折讓確認通知
// $operation->allowanceInvalidConfirmNotify('AA123456780001AB'); // 作廢折讓確認通知

// 發送對象：
// $operation->notifyCustomer();                        // 僅發送給客戶
// $operation->notifyMerchant();                        // 僅發送給特店
// $operation->notifyAll();                             // 皆發送

// 發送請求
try {
    $response = $client->send($operation);
    $data = $response->getData();

    echo "=== 發送發票通知結果 ===\n";
    echo "RtnCode: " . ($data['RtnCode'] ?? 'N/A') . "\n";
    echo "RtnMsg: " . ($data['RtnMsg'] ?? 'N/A') . "\n";

    if ($response->success()) {
        echo "\n發送成功！\n";
    } else {
        echo "\n發送失敗\n";
    }

    echo "\n完整回應資料：\n";
    print_r($data);
} catch (Exception $e) {
    echo "錯誤: " . $e->getMessage() . "\n";
}

