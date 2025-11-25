<?php

/**
 * 開立折讓發票範例程式碼。
 *
 * 賣方開立折讓單：根據財政部電子發票 MIG 4.1 版規定，B2B電子發票折讓作業僅能由原發票開立人（即賣方）發動。
 * 當特店（營業人）發票開立後，如發生銷貨退回、調換貨物或折讓等情形，應與買方（交易相對人）達成協議後，
 * 由賣方發起折讓流程。
 *
 * @see https://developers.ecpay.com.tw/?p=14923
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use ecPay\eInvoiceB2B\DTO\AllowanceItemDto;
use ecPay\eInvoiceB2B\EcPayClient;
use ecPay\eInvoiceB2B\Operations\Allowance;
use ecPay\eInvoiceB2B\Parameter\TaxType;
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

// 建立開立折讓發票請求
$operation = new Allowance($merchantId, $hashKey, $hashIV);

// 設定折讓基本資訊
$operation
    ->setRelateNumber('ALWN' . date('YmdHis'))  // 自訂編號（唯一值）
    ->setAllowanceDate(date('Y-m-d'))            // 折讓開立日期
    ->setInvoiceNumber('SA37758327')             // 原發票號碼
    ->setInvoiceDate('2019-08-31')               // 原發票開立日期
    ->setBuyerIdentifier('12345678')             // 買方統一編號
    ->setBuyerName('測試公司')                    // 買方名稱
    ->setAllowanceAmount(100)                     // 折讓金額
    ->setTaxAmount(5);                            // 折讓稅額

// 新增折讓商品項目
$operation->addItem(new AllowanceItemDto(
    '折讓商品A',  // 商品名稱
    1,             // 數量
    '個',          // 單位
    100,           // 單價
    100,           // 合計
    TaxType::TAXABLE,  // 課稅類別
    5              // 稅額（選填）
));

// 或使用陣列方式新增商品
// $operation->addItemsFromArray([
//     ['ItemName' => '折讓商品A', 'ItemCount' => 1, 'ItemWord' => '個', 'ItemPrice' => 100, 'ItemAmount' => 100, 'TaxType' => '1'],
// ]);

// 發送請求
try {
    $response = $client->send($operation);
    $data = $response->getData();

    echo "=== 開立折讓發票結果 ===\n";
    echo "RtnCode: " . ($data['RtnCode'] ?? 'N/A') . "\n";
    echo "RtnMsg: " . ($data['RtnMsg'] ?? 'N/A') . "\n";

    if ($response->success()) {
        echo "\n折讓開立成功！\n";
        echo "折讓號碼: " . ($data['RtnData']['AllowanceNumber'] ?? 'N/A') . "\n";
        echo "折讓日期: " . ($data['RtnData']['AllowanceDate'] ?? 'N/A') . "\n";
    } else {
        echo "\n折讓開立失敗\n";
    }

    echo "\n完整回應資料：\n";
    print_r($data);
} catch (Exception $e) {
    echo "錯誤: " . $e->getMessage() . "\n";
}

