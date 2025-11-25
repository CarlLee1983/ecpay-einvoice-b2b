<?php

/**
 * 查詢退回發票範例程式碼。
 *
 * 特店(營業人)可使用此 API 查詢已退回發票資訊，包括銷項發票及進項發票。
 *
 * @see https://developers.ecpay.com.tw/?p=14958
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use ecPay\eInvoiceB2B\EcPayClient;
use ecPay\eInvoiceB2B\Queries\GetReject;
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

// 建立查詢退回發票請求
$query = new GetReject($merchantId, $hashKey, $hashIV);

// 方式一：使用發票號碼查詢（需搭配發票日期）
$query
    ->salesInvoice()                    // 查詢銷項發票
    ->setInvoiceNumber('SA37758327')    // 發票號碼
    ->setInvoiceDate('2019-08-31');     // 發票日期（當有發票號碼時必填）

// 方式二：使用自訂編號查詢
// $query
//     ->salesInvoice()
//     ->setRelateNumber('2019081602');

// 也可以查詢進項發票：
// ->purchaseInvoice()

// 發送請求
try {
    $response = $client->send($query);
    $data = $response->getData();

    echo "=== 查詢退回發票結果 ===\n";
    echo "RtnCode: " . ($data['RtnCode'] ?? 'N/A') . "\n";
    echo "RtnMsg: " . ($data['RtnMsg'] ?? 'N/A') . "\n";

    if ($response->success()) {
        echo "\n查詢成功！\n";

        if (isset($data['RtnData'])) {
            $rtnData = $data['RtnData'];

            echo "\n--- 退回發票資訊 ---\n";
            echo "特店編號: " . ($rtnData['MerchantID'] ?? 'N/A') . "\n";
            echo "發票號碼: " . ($rtnData['InvoiceNumber'] ?? 'N/A') . "\n";
            echo "買方統編: " . ($rtnData['Buyer_Identifier'] ?? 'N/A') . "\n";
            echo "賣方統編: " . ($rtnData['Seller_Identifier'] ?? 'N/A') . "\n";
            echo "退回日期: " . ($rtnData['RejectDate'] ?? 'N/A') . "\n";
            echo "退回原因: " . ($rtnData['RejectReason'] ?? '') . "\n";
            echo "上傳狀態: " . ($rtnData['Upload_Status'] ?? 'N/A') . "\n";
            echo "上傳時間: " . ($rtnData['Upload_Date'] ?? 'N/A') . "\n";
            echo "確認日期: " . ($rtnData['ConfirmDate'] ?? '未確認') . "\n";
            echo "確認狀態: " . (($rtnData['ExchangeStatus'] ?? '') === '1' ? '已確認' : '未確認') . "\n";
            echo "備註: " . ($rtnData['Remark'] ?? '') . "\n";
        }
    } else {
        echo "\n查詢失敗\n";
    }

    echo "\n完整回應資料：\n";
    print_r($data);
} catch (Exception $e) {
    echo "錯誤: " . $e->getMessage() . "\n";
}

