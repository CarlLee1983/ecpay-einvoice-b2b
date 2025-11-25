<?php

/**
 * 查詢作廢折讓發票確認範例程式碼。
 *
 * 特店(營業人)可使用此 API 查詢已作廢發票折讓是否完成確認資訊。
 *
 * @see https://developers.ecpay.com.tw/?p=14983
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use ecPay\eInvoiceB2B\EcPayClient;
use ecPay\eInvoiceB2B\Queries\GetAllowanceInvalidConfirm;
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

// 建立查詢作廢折讓發票確認請求
$query = new GetAllowanceInvalidConfirm($merchantId, $hashKey, $hashIV);

// 方式一：使用折讓號碼查詢（需搭配折讓日期）
$query
    ->salesInvoice()                         // 查詢銷項發票作廢折讓
    ->setAllowanceNumber('SA377583270001')   // 折讓號碼（14碼）
    ->setAllowanceDate('2019-08-31');        // 折讓日期（當有折讓號碼時必填）

// 方式二：使用自訂編號查詢
// $query
//     ->salesInvoice()
//     ->setRelateNumber('2019081602');

// 也可以查詢進項發票作廢折讓確認狀態：
// ->purchaseInvoice()

// 發送請求
try {
    $response = $client->send($query);
    $data = $response->getData();

    echo "=== 查詢作廢折讓發票確認結果 ===\n";
    echo "RtnCode: " . ($data['RtnCode'] ?? 'N/A') . "\n";
    echo "RtnMsg: " . ($data['RtnMsg'] ?? 'N/A') . "\n";

    if ($response->success()) {
        echo "\n查詢成功！\n";

        if (isset($data['RtnData'])) {
            $rtnData = $data['RtnData'];

            echo "\n--- 作廢折讓發票確認資訊 ---\n";
            echo "特店編號: " . ($rtnData['MerchantID'] ?? 'N/A') . "\n";
            echo "折讓號碼: " . ($rtnData['AllowanceNumber'] ?? 'N/A') . "\n";
            echo "原發票號碼: " . ($rtnData['InvoiceNumber'] ?? 'N/A') . "\n";
            echo "買方統編: " . ($rtnData['BuyerId'] ?? 'N/A') . "\n";
            echo "賣方統編: " . ($rtnData['SellerId'] ?? 'N/A') . "\n";
            echo "作廢日期: " . ($rtnData['CancelDate'] ?? 'N/A') . "\n";
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

