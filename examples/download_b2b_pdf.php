<?php

/**
 * 發票列印 PDF 範例程式碼。
 *
 * 特店可使用此 API 取得單一發票 PDF 檔。
 *
 * 注意：同一 IP，10秒內最多只可呼叫 2 次。
 *
 * @see https://developers.ecpay.com.tw/?p=53383
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use CarlLee\EcPayB2B\EcPayClient;
use CarlLee\EcPayB2B\Printing\DownloadB2BPdf;
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

// 建立發票列印 PDF 請求
$operation = new DownloadB2BPdf($merchantId, $hashKey, $hashIV);

// 設定列印參數
$operation
    ->setInvoiceNumber('SA37758327')      // 發票號碼
    ->setInvoiceDate('2019-08-31');       // 發票開立日期

// 發送請求
try {
    $response = $client->send($operation);
    $data = $response->getData();

    echo "=== 發票列印 PDF 結果 ===\n";
    echo "RtnCode: " . ($data['RtnCode'] ?? 'N/A') . "\n";
    echo "RtnMsg: " . ($data['RtnMsg'] ?? 'N/A') . "\n";

    if ($response->success()) {
        echo "\n取得成功！\n";

        // 處理 PDF 資料
        // 注意：實際回傳的 PDF 資料可能需要特別處理
        if (isset($data['RtnData']['PdfData'])) {
            // 解碼 Base64 並儲存為 PDF 檔案
            $pdfContent = base64_decode($data['RtnData']['PdfData']);
            $filename = 'invoice_' . $operation->getPayload()['Data']['InvoiceNumber'] . '.pdf';

            if (file_put_contents($filename, $pdfContent)) {
                echo "PDF 已儲存至: " . $filename . "\n";
            }
        }
    } else {
        echo "\n取得失敗\n";
    }

    echo "\n完整回應資料：\n";
    print_r($data);
} catch (Exception $e) {
    echo "錯誤: " . $e->getMessage() . "\n";
}

