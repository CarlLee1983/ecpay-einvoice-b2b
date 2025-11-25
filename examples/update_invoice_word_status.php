<?php

/**
 * 設定字軌號碼狀態範例程式碼。
 *
 * 營業人(特店)新增字軌後，字軌的預設狀態皆為已審核且未啟用。
 * 如欲使用字軌，必須先設定狀態將字軌啟用。
 * 在開立發票之前，必須先將已新增完成的字軌做狀態的設定。
 *
 * @see https://developers.ecpay.com.tw/?p=14840
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use CarlLee\EcPayB2B\EcPayClient;
use CarlLee\EcPayB2B\Operations\UpdateInvoiceWordStatus;
use CarlLee\EcPayB2B\Parameter\InvoiceWordStatus;
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

// 建立設定字軌狀態請求
$operation = new UpdateInvoiceWordStatus($merchantId, $hashKey, $hashIV);

// 設定參數
// TrackID 為新增字軌後取得的字軌號碼 ID
$operation
    ->setTrackID('1234567890')  // 必填：字軌號碼 ID
    ->setInvoiceStatus(InvoiceWordStatus::ENABLED);  // 必填：發票字軌狀態

// 也可以使用便捷方法設定狀態：
// $operation->enable();   // 啟用
// $operation->suspend();  // 暫停
// $operation->disable();  // 停用（注意：停用後該字軌區間無法上傳發票）

// 發送請求
try {
    $response = $client->send($operation);
    $data = $response->getData();

    echo "=== 設定字軌狀態結果 ===\n";
    echo "RtnCode: " . ($data['RtnCode'] ?? 'N/A') . "\n";
    echo "RtnMsg: " . ($data['RtnMsg'] ?? 'N/A') . "\n";

    if ($response->success()) {
        echo "\n字軌狀態設定成功！\n";
    } else {
        echo "\n字軌狀態設定失敗\n";
    }

    echo "\n完整回應資料：\n";
    print_r($data);
} catch (Exception $e) {
    echo "錯誤: " . $e->getMessage() . "\n";
}

