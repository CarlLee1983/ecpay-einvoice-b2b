<?php

/**
 * 查詢發票確認範例程式碼。
 *
 * 特店(營業人)可使用此 API 查詢已開立發票是否完成確認資訊，包括銷項發票及進項發票。
 *
 * @see https://developers.ecpay.com.tw/?p=14940
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use CarlLee\EcPayB2B\EcPayClient;
use CarlLee\EcPayB2B\Queries\GetIssueConfirm;
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

// 建立查詢發票確認請求
$query = new GetIssueConfirm($merchantId, $hashKey, $hashIV);

// 方式一：使用發票號碼查詢（需搭配發票日期）
$query
    ->salesInvoice()                    // 查詢銷項發票
    ->setInvoiceNumber('SA37758327')    // 發票號碼
    ->setInvoiceDate('2019-08-31');     // 發票日期（當有發票號碼時必填）

// 方式二：使用自訂編號查詢
// $query
//     ->salesInvoice()
//     ->setRelateNumber('2019081602');

// 可選篩選條件
// ->setSellerIdentifier('12345678')           // 賣家統編
// ->setBuyerIdentifier('87654321')            // 買家統編
// ->setInvoiceDateRange('2024-01-01', '2024-01-31')  // 日期區間
// ->setInvoiceNumberRange('00000001', '00000100')    // 號碼區間（不含字軌）
// ->setIssueStatus('1')                       // 發票狀態：0=退回, 1=開立
// ->setInvalidStatus('0')                     // 作廢狀態：0=未作廢, 1=已作廢
// ->setExchangeMode('1')                      // 上傳模式：0=存證, 1=交換
// ->setExchangeStatus('1')                    // 交換進度
// ->setUploadStatus('1')                      // 上傳狀態：0=未上傳, 1=已上傳, 2=失敗

// 發送請求
try {
    $response = $client->send($query);
    $data = $response->getData();

    echo "=== 查詢發票確認結果 ===\n";
    echo "RtnCode: " . ($data['RtnCode'] ?? 'N/A') . "\n";
    echo "RtnMsg: " . ($data['RtnMsg'] ?? 'N/A') . "\n";

    if ($response->success()) {
        echo "\n查詢成功！\n";

        if (isset($data['RtnData'])) {
            $rtnData = $data['RtnData'];

            // 如果是陣列（多筆結果）
            if (isset($rtnData[0])) {
                foreach ($rtnData as $index => $item) {
                    echo "\n--- 發票 " . ($index + 1) . " ---\n";
                    $this->displayInvoiceInfo($item);
                }
            } else {
                // 單筆結果
                echo "\n--- 發票資訊 ---\n";
                echo "特店編號: " . ($rtnData['MerchantID'] ?? 'N/A') . "\n";
                echo "發票號碼: " . ($rtnData['InvoiceNumber'] ?? 'N/A') . "\n";
                echo "發票日期: " . ($rtnData['InvoiceDate'] ?? 'N/A') . "\n";
                echo "買方統編: " . ($rtnData['Buyer_Identifier'] ?? 'N/A') . "\n";
                echo "賣方統編: " . ($rtnData['Seller_Identifier'] ?? 'N/A') . "\n";
                echo "確認日期: " . ($rtnData['ConfirmDate'] ?? '未確認') . "\n";
                echo "上傳狀態: " . ($rtnData['Upload_Status'] ?? 'N/A') . "\n";
                echo "上傳時間: " . ($rtnData['Upload_Date'] ?? 'N/A') . "\n";
                echo "備註: " . ($rtnData['ConfirmRemark'] ?? '') . "\n";
            }
        }
    } else {
        echo "\n查詢失敗\n";
    }

    echo "\n完整回應資料：\n";
    print_r($data);
} catch (Exception $e) {
    echo "錯誤: " . $e->getMessage() . "\n";
}

