# 綠界電子發票 B2B API 套件

ECPay e-Invoice B2B API wrapper（交換模式），提供基於 DTO 的操作介面，支援 Laravel 整合與沙盒測試環境。

> 📖 官方技術文件：[B2B電子發票API技術文件 (交換模式)](https://developers.ecpay.com.tw/?p=14825)

## 功能特色

- ✅ 完整實作 27 個 B2B 電子發票 API
- ✅ 類型安全的參數常數類別
- ✅ 完整的驗證機制
- ✅ Laravel 整合支援
- ✅ 484 個單元測試

## 安裝

```bash
composer require carllee1983/ecpay-einvoice-b2b
```

## 參數

* Server: 介接網址
* MerchantID: 特約店代碼
* HashKey
* HashIV

## 環境設定

### 測試環境
```
Server: https://einvoice-stage.ecpay.com.tw (TCP 443)
MerchantID: 2000132
HashKey: ejCk326UnaZWKisg
HashIV: q9jcZX8Ib9LM8wYk
```

### 正式環境
```
Server: https://einvoice.ecpay.com.tw (TCP 443)
```

## 介接注意事項

根據[綠界官方文件](https://developers.ecpay.com.tw/?p=14825)，使用本套件時請注意：

1. **HTTPS 連線**：僅支援 HTTPS (443 port) 連線方式，請使用合法的 DNS 進行介接。
2. **TLS 版本**：支援 TLS 1.1 以上之加密通訊協定（本套件已自動設定）。
3. **HTTP POST**：所有 API 請求皆使用 HTTP POST 方式傳送。
4. **金鑰安全**：請勿將金鑰資訊（HashKey、HashIV）存放或顯示於前端網頁內，避免金鑰被盜取。
5. **防火牆設定**：
   - 連接綠界主機請以 FQDN 方式設定：
     - 正式環境：`einvoice.ecpay.com.tw TCP 443`
     - 測試環境：`einvoice-stage.ecpay.com.tw TCP 443`
   - 允許綠界主機連入：
     - 正式環境：`postgate.ecpay.com.tw TCP 443`
     - 測試環境：`postgate-stage.ecpay.com.tw TCP 443`
6. **API 呼叫頻率**：呼叫速度太快會收到 HTTP 403，請降低呼叫頻率並等候 30 分鐘後重試。
7. **中文網址**：回傳網址不支援中文，請使用 punycode 編碼。
8. **服務申請**：使用電子發票服務需與綠界提出申請方可使用。

### SSL 驗證設定

```php
use ecPay\eInvoiceB2B\Request;

// 測試環境可關閉 SSL 驗證（不建議用於正式環境）
Request::setVerifySsl(false);

// 正式環境請啟用 SSL 驗證（預設）
Request::setVerifySsl(true);
```

## 快速開始

```php
use ecPay\eInvoiceB2B\EcPayClient;
use ecPay\eInvoiceB2B\Operations\Issue;
use ecPay\eInvoiceB2B\Request;

$server = 'https://einvoice-stage.ecpay.com.tw';
$merchantId = '2000132';
$hashKey = 'ejCk326UnaZWKisg';
$hashIV = 'q9jcZX8Ib9LM8wYk';

// 測試環境關閉 SSL 驗證
Request::setVerifySsl(false);

// 初始化 Client
$client = new EcPayClient($server, $hashKey, $hashIV);

// 建立開立發票操作
$invoice = new Issue($merchantId, $hashKey, $hashIV);
$invoice
    ->setInvoiceNumber('AB12345678')
    ->setInvoiceDate('2024-01-15')
    ->setBuyerIdentifier('12345678')
    ->setBuyerName('測試公司')
    ->setSalesAmount(1000)
    ->setTaxAmount(50)
    ->setTotalAmount(1050);

// 發送請求
$response = $client->send($invoice);

if ($response->success()) {
    echo "發票開立成功！";
    print_r($response->getData());
}
```

## API 功能清單

### 前置作業 API

| 類別 | 說明 | 範例檔案 |
|------|------|----------|
| `MaintainMerchantCustomerData` | 交易對象維護 | `examples/maintain_merchant_customer_data.php` |
| `GetGovInvoiceWordSetting` | 查詢財政部配號結果 | `examples/get_gov_invoice_word_setting.php` |
| `AddInvoiceWordSetting` | 字軌與配號設定 | `examples/add_invoice_word_setting.php` |
| `UpdateInvoiceWordStatus` | 設定字軌號碼狀態 | `examples/update_invoice_word_status.php` |

### 發票作業 API

| 類別 | 說明 | 範例檔案 |
|------|------|----------|
| `Issue` | 開立發票 | `examples/issue.php` |
| `IssueConfirm` | 開立發票確認 | `examples/issue_confirm.php` |
| `Invalid` | 作廢發票 | `examples/invalid.php` |
| `InvalidConfirm` | 作廢發票確認 | `examples/invalid_confirm.php` |
| `Reject` | 退回發票 | `examples/reject.php` |
| `RejectConfirm` | 退回發票確認 | `examples/reject_confirm.php` |
| `Allowance` | 開立折讓發票 | `examples/allowance.php` |
| `AllowanceConfirm` | 折讓發票確認 | `examples/allowance_confirm.php` |
| `AllowanceInvalid` | 作廢折讓發票 | `examples/allowance_invalid.php` |
| `AllowanceInvalidConfirm` | 作廢折讓發票確認 | `examples/allowance_invalid_confirm.php` |

### 查詢作業 API

| 類別 | 說明 | 範例檔案 |
|------|------|----------|
| `GetIssue` | 查詢發票 | `examples/get_issue.php` |
| `GetIssueConfirm` | 查詢發票確認 | `examples/get_issue_confirm.php` |
| `GetInvalid` | 查詢作廢發票 | `examples/get_invalid.php` |
| `GetInvalidConfirm` | 查詢作廢發票確認 | `examples/get_invalid_confirm.php` |
| `GetReject` | 查詢退回發票 | `examples/get_reject.php` |
| `GetRejectConfirm` | 查詢退回發票確認 | `examples/get_reject_confirm.php` |
| `GetAllowance` | 查詢折讓發票 | `examples/get_allowance.php` |
| `GetAllowanceConfirm` | 查詢折讓發票確認 | `examples/get_allowance_confirm.php` |
| `GetAllowanceInvalid` | 查詢作廢折讓發票 | `examples/get_allowance_invalid.php` |
| `GetAllowanceInvalidConfirm` | 查詢作廢折讓發票確認 | `examples/get_allowance_invalid_confirm.php` |

### 通知與列印 API

| 類別 | 說明 | 範例檔案 |
|------|------|----------|
| `Notify` | 發送發票通知 | `examples/notify.php` |
| `InvoicePrint` | 發票列印（取得列印網址） | `examples/invoice_print.php` |
| `DownloadB2BPdf` | 發票列印 PDF | `examples/download_b2b_pdf.php` |

## 使用範例

### 開立發票

```php
use ecPay\eInvoiceB2B\Operations\Issue;
use ecPay\eInvoiceB2B\DTO\InvoiceItemDto;
use ecPay\eInvoiceB2B\DTO\ItemCollection;
use ecPay\eInvoiceB2B\Parameter\TaxType;

$invoice = new Issue($merchantId, $hashKey, $hashIV);

// 設定發票基本資料
$invoice
    ->setInvoiceNumber('AB12345678')
    ->setInvoiceDate('2024-01-15')
    ->setInvoiceTime('10:30:00')
    ->setBuyerIdentifier('12345678')
    ->setBuyerName('測試公司')
    ->setTaxType(TaxType::TAXABLE)
    ->setSalesAmount(1000)
    ->setTaxAmount(50)
    ->setTotalAmount(1050);

// 新增商品項目
$items = new ItemCollection([
    new InvoiceItemDto('商品A', 2, '個', 250, 500, 25),
    new InvoiceItemDto('商品B', 1, '件', 500, 500, 25),
]);
$invoice->setItems($items);

$response = $client->send($invoice);
```

### 作廢發票

```php
use ecPay\eInvoiceB2B\Operations\Invalid;
use ecPay\eInvoiceB2B\Parameter\InvalidReason;

$invalid = new Invalid($merchantId, $hashKey, $hashIV);
$invalid
    ->setInvoiceNumber('AB12345678')
    ->setInvoiceDate('2024-01-15')
    ->setInvalidReason(InvalidReason::SALES_RETURN)
    ->setRemark('客戶退貨');

$response = $client->send($invalid);
```

### 開立折讓發票

```php
use ecPay\eInvoiceB2B\Operations\Allowance;
use ecPay\eInvoiceB2B\DTO\AllowanceItemDto;
use ecPay\eInvoiceB2B\DTO\ItemCollection;

$allowance = new Allowance($merchantId, $hashKey, $hashIV);
$allowance
    ->setAllowanceNo('AA12345678901234')
    ->setAllowanceDate('2024-01-20')
    ->setBuyerIdentifier('12345678')
    ->setBuyerName('測試公司')
    ->setTaxAmount(10)
    ->setTotalAmount(200);

// 新增折讓項目
$items = new ItemCollection([
    new AllowanceItemDto('AB12345678', '2024-01-15', '商品A', 1, '個', 200, 200, 10),
]);
$allowance->setItems($items);

$response = $client->send($allowance);
```

### 查詢發票

```php
use ecPay\eInvoiceB2B\Queries\GetIssue;
use ecPay\eInvoiceB2B\Parameter\B2BInvoiceCategory;

$query = new GetIssue($merchantId, $hashKey, $hashIV);
$query
    ->setInvoiceCategory(B2BInvoiceCategory::SALES)
    ->setInvoiceNumber('AB12345678')
    ->setInvoiceDate('2024-01-15');

$response = $client->send($query);
```

### 發送發票通知

```php
use ecPay\eInvoiceB2B\Notifications\Notify;
use ecPay\eInvoiceB2B\Parameter\InvoiceTag;
use ecPay\eInvoiceB2B\Parameter\NotifyTarget;

$notify = new Notify($merchantId, $hashKey, $hashIV);
$notify
    ->setInvoiceNumber('AB12345678')
    ->setInvoiceDate('2024-01-15')
    ->setNotifyMail('customer@example.com')
    ->issueNotify()      // 發票開立通知
    ->notifyAll();       // 發送給所有人

$response = $client->send($notify);
```

### 發票列印

```php
use ecPay\eInvoiceB2B\Printing\InvoicePrint;

$print = new InvoicePrint($merchantId, $hashKey, $hashIV);
$print
    ->setInvoiceNumber('AB12345678')
    ->setInvoiceDate('2024-01-15');

$response = $client->send($print);

// 取得列印網址
$printUrl = $response->getData()['RtnData']['PrintUrl'] ?? null;
```

## 參數常數類別

本套件提供多個參數常數類別，確保類型安全：

| 類別 | 說明 | 位置 |
|------|------|------|
| `TaxType` | 課稅類別（應稅/零稅率/免稅/特種稅額） | `Parameter\TaxType` |
| `ZeroTaxRate` | 零稅率類型（非經海關出口/經海關出口） | `Parameter\ZeroTaxRate` |
| `SpecialTaxType` | 特種稅額類別（娛樂業/小規模營業人） | `Parameter\SpecialTaxType` |
| `InvType` | 發票類型（一般/特種） | `Parameter\InvType` |
| `B2BInvoiceCategory` | 發票類別（銷項/進項） | `Parameter\B2BInvoiceCategory` |
| `ExchangeMode` | 交換模式（存證/交換） | `Parameter\ExchangeMode` |
| `ConfirmAction` | 確認動作（確認/退回） | `Parameter\ConfirmAction` |
| `InvalidReason` | 作廢原因 | `Parameter\InvalidReason` |
| `InvoiceTag` | 發送內容類型（1-10） | `Parameter\InvoiceTag` |
| `NotifyTarget` | 發送對象（客戶/特店/皆發送） | `Parameter\NotifyTarget` |
| `CustomerType` | 交易對象類型（客戶/供應商/皆為） | `Parameter\CustomerType` |
| `MaintainAction` | 維護動作（新增/更新/刪除） | `Parameter\MaintainAction` |
| `InvoiceWordStatus` | 字軌狀態（使用中/待使用/停用） | `Parameter\InvoiceWordStatus` |

### 使用範例

```php
use ecPay\eInvoiceB2B\Parameter\TaxType;

// 使用常數
$invoice->setTaxType(TaxType::TAXABLE);

// 驗證值
if (TaxType::isValid($value)) {
    // ...
}
```

## 模組分群

```
ecPay\eInvoiceB2B\
├── Operations\         # 發票作業（開立、作廢、退回、折讓等）
├── Queries\            # 查詢作業
├── Notifications\      # 發送通知
├── Printing\           # 發票列印
├── Parameter\          # 參數常數
└── DTO\                # 資料傳輸物件
```

> 以上模組皆繼承共同的 `Content` 基底類別，可透過相同的 `EcPayClient` 傳送請求。

## 工廠模式

`OperationFactory` 可依別名快速建立操作物件並注入共用憑證。

```php
use ecPay\eInvoiceB2B\Factories\OperationFactory;

$factory = new OperationFactory([
    'merchant_id' => $merchantId,
    'hash_key' => $hashKey,
    'hash_iv' => $hashIV,
]);

$invoice = $factory->make('issue');
$query = $factory->make('get_issue');
```

## Laravel 整合

### 安裝設定

套件已支援 auto-discovery，或可手動在 `config/app.php` 註冊：

```php
'providers' => [
    ecPay\eInvoiceB2B\Laravel\EcPayServiceProvider::class,
],
```

### 發布設定檔

```bash
php artisan vendor:publish --tag=ecpay-einvoice-b2b-config
```

### 環境變數設定

在 `.env` 檔案中加入：

```env
ECPAY_EINVOICE_B2B_SERVER=https://einvoice-stage.ecpay.com.tw
ECPAY_EINVOICE_B2B_MERCHANT_ID=2000132
ECPAY_EINVOICE_B2B_HASH_KEY=ejCk326UnaZWKisg
ECPAY_EINVOICE_B2B_HASH_IV=q9jcZX8Ib9LM8wYk
```

### 使用 Facade

```php
use ecPay\eInvoiceB2B\Laravel\Facades\EcPayInvoice;
use ecPay\eInvoiceB2B\Laravel\Facades\EcPayQuery;

$invoice = EcPayInvoice::issue();
$query = EcPayQuery::getIssue();
```

### 透過容器解析

```php
$invoice = app('ecpay-b2b.issue');
$client = app('ecpay-b2b.client');
```

## 目錄結構

```
src/
├── AES.php                      # 加密 Trait（相容性保留）
├── Content.php                  # 所有操作的基底類別
├── EcPayClient.php              # 主要客戶端
├── Request.php                  # HTTP 請求處理
├── Response.php                 # 回應處理
├── InvoiceInterface.php         # 發票介面
├── Contracts/                   # 契約介面
├── DTO/                         # 資料傳輸物件
│   ├── InvoiceItemDto.php       # 發票商品項目
│   ├── AllowanceItemDto.php     # 折讓商品項目
│   └── ItemCollection.php       # 項目集合
├── Infrastructure/              # 基礎設施（加密、編碼）
│   ├── CipherService.php        # AES 加解密服務
│   └── PayloadEncoder.php       # Payload 編碼器
├── Factories/                   # 工廠模式
├── Laravel/                     # Laravel 整合
├── Operations/                  # 發票作業類別
│   ├── Issue.php                # 開立發票
│   ├── IssueConfirm.php         # 開立發票確認
│   ├── Invalid.php              # 作廢發票
│   ├── InvalidConfirm.php       # 作廢發票確認
│   ├── Reject.php               # 退回發票
│   ├── RejectConfirm.php        # 退回發票確認
│   ├── Allowance.php            # 開立折讓發票
│   ├── AllowanceConfirm.php     # 折讓發票確認
│   ├── AllowanceInvalid.php     # 作廢折讓發票
│   ├── AllowanceInvalidConfirm.php  # 作廢折讓發票確認
│   ├── MaintainMerchantCustomerData.php  # 交易對象維護
│   ├── AddInvoiceWordSetting.php    # 字軌與配號設定
│   └── UpdateInvoiceWordStatus.php  # 設定字軌號碼狀態
├── Queries/                     # 查詢類別
│   ├── GetIssue.php             # 查詢發票
│   ├── GetIssueConfirm.php      # 查詢發票確認
│   ├── GetInvalid.php           # 查詢作廢發票
│   ├── GetInvalidConfirm.php    # 查詢作廢發票確認
│   ├── GetReject.php            # 查詢退回發票
│   ├── GetRejectConfirm.php     # 查詢退回發票確認
│   ├── GetAllowance.php         # 查詢折讓發票
│   ├── GetAllowanceConfirm.php  # 查詢折讓發票確認
│   ├── GetAllowanceInvalid.php  # 查詢作廢折讓發票
│   ├── GetAllowanceInvalidConfirm.php  # 查詢作廢折讓發票確認
│   ├── GetGovInvoiceWordSetting.php    # 查詢財政部配號結果
│   └── GetInvoiceWordSetting.php       # 查詢字軌
├── Parameter/                   # 參數常數
│   ├── TaxType.php              # 課稅類別
│   ├── ZeroTaxRate.php          # 零稅率類型
│   ├── SpecialTaxType.php       # 特種稅額類別
│   ├── InvType.php              # 發票類型
│   ├── B2BInvoiceCategory.php   # 發票類別
│   ├── ExchangeMode.php         # 交換模式
│   ├── ConfirmAction.php        # 確認動作
│   ├── InvalidReason.php        # 作廢原因
│   ├── InvoiceTag.php           # 發送內容類型
│   ├── NotifyTarget.php         # 發送對象
│   ├── CustomerType.php         # 交易對象類型
│   ├── MaintainAction.php       # 維護動作
│   └── InvoiceWordStatus.php    # 字軌狀態
├── Notifications/               # 通知類別
│   └── Notify.php               # 發送發票通知
└── Printing/                    # 列印類別
    ├── InvoicePrint.php         # 發票列印
    └── DownloadB2BPdf.php       # 發票列印 PDF
```

## 開發指令

```bash
# 執行測試
composer test

# 程式碼風格檢查
composer phpcs

# 程式碼風格修正
composer phpcbf
```

## 測試

本套件包含完整的單元測試：

```bash
./vendor/bin/phpunit
```

測試覆蓋率：**484 tests, 785 assertions**

## 授權

MIT License

---

# ECPay e-Invoice B2B API Package (English Overview)

This library wraps the official ECPay e-Invoice B2B API (Exchange Mode) with full implementation of all 27 APIs.

## Features

- ✅ Complete implementation of 27 B2B e-invoice APIs
- ✅ Type-safe parameter constants
- ✅ Comprehensive validation
- ✅ Laravel integration support
- ✅ 484 unit tests

## Parameters

- Server: API endpoint (stage or production)
- MerchantID: merchant code registered with ECPay
- HashKey / HashIV: AES credentials for encrypting `Data`

## Quick Start

```php
use ecPay\eInvoiceB2B\EcPayClient;
use ecPay\eInvoiceB2B\Operations\Issue;

$client = new EcPayClient($server, $hashKey, $hashIV);

$invoice = new Issue($merchantId, $hashKey, $hashIV);
$invoice
    ->setInvoiceNumber('AB12345678')
    ->setInvoiceDate('2024-01-15')
    ->setBuyerIdentifier('12345678')
    ->setBuyerName('Test Company')
    ->setSalesAmount(1000)
    ->setTaxAmount(50)
    ->setTotalAmount(1050);

$response = $client->send($invoice);
```

## Module Groups

- `Operations\*`: create/void invoices and allowances
- `Queries\*`: lookup invoice/allowance status
- `Notifications\*`: push notifications
- `Printing\*`: invoice printing helpers

All modules extend `Content`, so you can share the same `EcPayClient` to send requests.
