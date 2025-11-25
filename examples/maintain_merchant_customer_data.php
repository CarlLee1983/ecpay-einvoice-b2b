<?php

/**
 * 交易對象維護範例程式碼。
 *
 * B2B 電子發票分為交換與存證模式，在串接此規格文件前，
 * 必須先用此 API 設定交易對象(買方/賣方/買賣方)、設定開立形式(交換/存證)
 * 以及新增交易對象的相關資訊。
 *
 * 注意：同一個統編下若有多個 MerchantID，這些 MerchantID 僅能以「存證」開立方式
 * 來建立相同交易對象。
 *
 * @see https://developers.ecpay.com.tw/?p=14830
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use ecPay\eInvoiceB2B\EcPayClient;
use ecPay\eInvoiceB2B\Operations\MaintainMerchantCustomerData;
use ecPay\eInvoiceB2B\Parameter\CustomerType;
use ecPay\eInvoiceB2B\Parameter\ExchangeMode;
use ecPay\eInvoiceB2B\Parameter\MaintainAction;
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

// 建立交易對象維護請求
$operation = new MaintainMerchantCustomerData($merchantId, $hashKey, $hashIV);

// 設定參數 - 新增交易對象
$operation
    ->add()                                      // 動作：新增（或使用 setAction(MaintainAction::ADD)）
    ->setIdentifier('53538851')                  // 統一編號（8 碼數字，必填）
    ->setType(CustomerType::SELLER)              // 交易對象：賣方（或使用 asSeller()）
    ->setCompanyName('測試公司')                  // 公司名稱
    ->setAddress('台北市內湖區測試路1號')          // 公司地址
    ->setTelephoneNumber('02-12345678')          // 公司電話
    ->setExchangeMode(ExchangeMode::EXCHANGE)    // 開立形式：交換（或使用 exchangeMode()）
    ->setEmailAddress('test@example.com');       // 公司信箱（必填，可用陣列傳入多組）

// 也可以使用便捷方法：
// ->asBuyer()       // 設定為買方
// ->asSeller()      // 設定為賣方
// ->asBoth()        // 設定為買賣方
// ->archiveMode()   // 設定為存證模式
// ->exchangeMode()  // 設定為交換模式

// 多組信箱可以用陣列傳入：
// ->setEmailAddress(['abc@pay.com.tw', 'def@pay.com.tw', 'ghi@pay.com.tw'])

// 發送請求
try {
    $response = $client->send($operation);
    $data = $response->getData();

    echo "=== 交易對象維護結果 ===\n";
    echo "RtnCode: " . ($data['RtnCode'] ?? 'N/A') . "\n";
    echo "RtnMsg: " . ($data['RtnMsg'] ?? 'N/A') . "\n";

    if ($response->success()) {
        echo "\n操作成功！\n";
    } else {
        echo "\n操作失敗\n";
    }

    echo "\n完整回應資料：\n";
    print_r($data);
} catch (Exception $e) {
    echo "錯誤: " . $e->getMessage() . "\n";
}

