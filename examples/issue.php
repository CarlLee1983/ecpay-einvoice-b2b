<?php

/**
 * 開立發票範例程式碼。
 *
 * 特店(營業人)傳送開立發票參數給綠界科技加值中心(以下簡稱綠界)後，
 * 由綠界暫存相關資料。綠界會於隔日開立發票後上傳至財政部電子發票整合服務平台。
 *
 * @see https://developers.ecpay.com.tw/?p=14850
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use CarlLee\EcPayB2B\DTO\InvoiceItemDto;
use CarlLee\EcPayB2B\EcPayClient;
use CarlLee\EcPayB2B\Operations\Issue;
use CarlLee\EcPayB2B\Parameter\ExchangeMode;
use CarlLee\EcPayB2B\Parameter\InvType;
use CarlLee\EcPayB2B\Parameter\TaxType;
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

// 建立開立發票請求
$operation = new Issue($merchantId, $hashKey, $hashIV);

// 設定發票基本資訊
$operation
    ->setRelateNumber('TEST' . date('YmdHis'))  // 自訂編號（唯一值）
    ->setInvoiceDate(date('Y-m-d'))              // 發票開立日期
    ->setInvType(InvType::GENERAL)               // 一般稅額發票
    ->setExchangeMode(ExchangeMode::EXCHANGE)    // 交換模式
    ->setBuyerIdentifier('12345678')             // 買方統一編號
    ->setBuyerName('測試公司')                    // 買方名稱
    ->setBuyerAddress('台北市信義區信義路五段7號')  // 買方地址（選填）
    ->setBuyerEmailAddress('test@example.com')   // 買方 Email（選填）
    ->setTaxType(TaxType::TAXABLE)               // 應稅
    ->setSalesAmount(1000)                        // 銷售額
    ->setTaxAmount(50)                            // 稅額
    ->setTotalAmount(1050)                        // 總金額
    ->setMainRemark('測試發票備註');              // 發票備註（選填）

// 新增商品項目
$operation->addItem(new InvoiceItemDto(
    '測試商品A',  // 商品名稱
    1,             // 數量
    '個',          // 單位
    500,           // 單價
    500,           // 合計
    25             // 稅額（選填，可由綠界代算）
));

$operation->addItem(new InvoiceItemDto(
    '測試商品B',
    2,
    '個',
    250,
    500,
    25
));

// 或使用陣列方式新增商品
// $operation->addItemsFromArray([
//     ['ItemName' => '商品A', 'ItemCount' => 1, 'ItemWord' => '個', 'ItemPrice' => 500, 'ItemAmount' => 500],
//     ['ItemName' => '商品B', 'ItemCount' => 2, 'ItemWord' => '個', 'ItemPrice' => 250, 'ItemAmount' => 500],
// ]);

// 發送請求
try {
    $response = $client->send($operation);
    $data = $response->getData();

    echo "=== 開立發票結果 ===\n";
    echo "RtnCode: " . ($data['RtnCode'] ?? 'N/A') . "\n";
    echo "RtnMsg: " . ($data['RtnMsg'] ?? 'N/A') . "\n";

    if ($response->success()) {
        echo "\n開立成功！\n";
        echo "發票號碼: " . ($data['RtnData']['InvoiceNumber'] ?? 'N/A') . "\n";
        echo "發票日期: " . ($data['RtnData']['InvoiceDate'] ?? 'N/A') . "\n";
        echo "隨機碼: " . ($data['RtnData']['RandomNumber'] ?? 'N/A') . "\n";
    } else {
        echo "\n開立失敗\n";
    }

    echo "\n完整回應資料：\n";
    print_r($data);
} catch (Exception $e) {
    echo "錯誤: " . $e->getMessage() . "\n";
}

