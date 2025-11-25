<?php

/**
 * 查詢發票範例程式碼。
 *
 * 特店(營業人)可使用此 API 查詢已開立發票資訊，包括銷項發票及進項發票，
 * 綠界會以回傳參數方式回覆該張發票資料。
 *
 * @see https://developers.ecpay.com.tw/?p=14935
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use ecPay\eInvoiceB2B\EcPayClient;
use ecPay\eInvoiceB2B\Parameter\B2BInvoiceCategory;
use ecPay\eInvoiceB2B\Queries\GetIssue;
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

// 建立查詢發票請求
$query = new GetIssue($merchantId, $hashKey, $hashIV);

// 設定查詢參數
$query
    ->salesInvoice()                    // 查詢銷項發票（或使用 setInvoiceCategory(B2BInvoiceCategory::SALES)）
    ->setInvoiceNumber('SA37758327')    // 發票號碼（2碼英文 + 8碼數字）
    ->setInvoiceDate('2019-08-31');     // 發票開立日期

// 也可以查詢進項發票：
// ->purchaseInvoice()  // 或 setInvoiceCategory(B2BInvoiceCategory::PURCHASE)

// 可選：設定自訂編號
// ->setRelateNumber('2019081602')

// 發送請求
try {
    $response = $client->send($query);
    $data = $response->getData();

    echo "=== 查詢發票結果 ===\n";
    echo "RtnCode: " . ($data['RtnCode'] ?? 'N/A') . "\n";
    echo "RtnMsg: " . ($data['RtnMsg'] ?? 'N/A') . "\n";

    if ($response->success()) {
        echo "\n查詢成功！\n";

        // 發票基本資訊
        echo "\n--- 發票基本資訊 ---\n";
        echo "發票號碼: " . ($data['RtnData']['InvoiceNumber'] ?? 'N/A') . "\n";
        echo "發票日期: " . ($data['RtnData']['InvoiceDate'] ?? 'N/A') . "\n";
        echo "自訂編號: " . ($data['RtnData']['RelateNumber'] ?? 'N/A') . "\n";
        echo "隨機碼: " . ($data['RtnData']['RandomNumber'] ?? 'N/A') . "\n";

        // 買方資訊
        echo "\n--- 買方資訊 ---\n";
        echo "統一編號: " . ($data['RtnData']['Buyer_Identifier'] ?? 'N/A') . "\n";
        echo "公司名稱: " . ($data['RtnData']['Buyer_Name'] ?? 'N/A') . "\n";
        echo "地址: " . ($data['RtnData']['Buyer_Address'] ?? 'N/A') . "\n";
        echo "電話: " . ($data['RtnData']['Buyer_TelephoneNumber'] ?? 'N/A') . "\n";
        echo "Email: " . ($data['RtnData']['Buyer_EmailAddress'] ?? 'N/A') . "\n";

        // 金額資訊
        echo "\n--- 金額資訊 ---\n";
        echo "稅別: " . ($data['RtnData']['TaxType'] ?? 'N/A') . "\n";
        echo "稅率: " . ($data['RtnData']['TaxRate'] ?? 'N/A') . "\n";
        echo "銷售額: " . ($data['RtnData']['SalesAmount'] ?? 'N/A') . "\n";
        echo "稅額: " . ($data['RtnData']['TaxAmount'] ?? 'N/A') . "\n";
        echo "總金額: " . ($data['RtnData']['TotalAmount'] ?? 'N/A') . "\n";
        echo "剩餘可折讓金額: " . ($data['RtnData']['BalanceAmount'] ?? 'N/A') . "\n";

        // 狀態資訊
        echo "\n--- 狀態資訊 ---\n";
        echo "開立狀態: " . (($data['RtnData']['Issue_Status'] ?? '') === '1' ? '已開立' : '已退回') . "\n";
        echo "作廢狀態: " . (($data['RtnData']['Invalid_Status'] ?? '') === '1' ? '已作廢' : '未作廢') . "\n";
        echo "上傳狀態: " . ($data['RtnData']['Upload_Status'] ?? 'N/A') . "\n";
        echo "開立方式: " . (($data['RtnData']['ExchangeMode'] ?? '') === '1' ? '交換' : '存證') . "\n";
        echo "確認狀態: " . (($data['RtnData']['ExchangeStatus'] ?? '') === '1' ? '已確認' : '未確認') . "\n";

        // 商品明細
        if (isset($data['RtnData']['Items']) && is_array($data['RtnData']['Items'])) {
            echo "\n--- 商品明細 ---\n";
            foreach ($data['RtnData']['Items'] as $index => $item) {
                echo "\n商品 " . ($index + 1) . ":\n";
                echo "  名稱: " . ($item['ItemName'] ?? 'N/A') . "\n";
                echo "  數量: " . ($item['ItemCount'] ?? 'N/A') . " " . ($item['ItemWord'] ?? '') . "\n";
                echo "  單價: " . ($item['ItemPrice'] ?? 'N/A') . "\n";
                echo "  金額: " . ($item['ItemAmount'] ?? 'N/A') . "\n";
                echo "  稅額: " . ($item['ItemTax'] ?? 'N/A') . "\n";
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

