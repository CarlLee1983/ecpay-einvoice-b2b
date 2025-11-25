# 綠界電子發票 B2B API 套件

ECPay e-Invoice B2B API wrapper（交換模式），提供基於 DTO 的操作介面，支援 Laravel 整合與沙盒測試環境。

> 📖 官方技術文件：[B2B電子發票API技術文件 (交換模式)](https://developers.ecpay.com.tw/?p=14825)

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

## 基本使用

```php
use ecPay\eInvoiceB2B\EcPayClient;

$server = 'https://einvoice-stage.ecpay.com.tw';
$merchantId = '2000132';
$hashKey = 'ejCk326UnaZWKisg';
$hashIV = 'q9jcZX8Ib9LM8wYk';

// 初始化 Client
$client = new EcPayClient($server, $hashKey, $hashIV);

// TODO: 操作類別待實作
```

## B2B API 功能清單

根據官方技術文件，B2B 電子發票 API（交換模式）支援以下功能：

### 前置作業
- 交易對象維護
- 查詢財政部配號結果
- 字軌與配號設定
- 設定字軌號碼狀態
- 查詢字軌

### 發票作業 API
- 開立發票 / 開立發票確認
- 作廢發票 / 作廢發票確認
- 退回發票 / 退回發票確認
- 開立折讓發票 / 折讓發票確認
- 作廢折讓發票 / 作廢折讓發票確認

### 查詢作業 API
- 查詢發票 / 查詢發票確認
- 查詢作廢發票 / 查詢作廢發票確認
- 查詢退回發票 / 查詢退回發票確認
- 查詢折讓發票 / 查詢折讓發票確認
- 查詢作廢折讓發票 / 查詢作廢折讓發票確認

### 發送通知
- 發送發票通知

### 發票列印
- 發票列印
- 發票列印 – PDF

## 模組分群

- `ecPay\eInvoiceB2B\Operations\*`：發票作業類別（開立、作廢、退回、折讓等）- 待實作
- `ecPay\eInvoiceB2B\Queries\*`：查詢與驗證類別 - 待實作
- `ecPay\eInvoiceB2B\Notifications\*`：發送通知類別 - 待實作
- `ecPay\eInvoiceB2B\Printing\*`：發票列印功能 - 待實作

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

// 待操作類別實作後使用
// $invoice = $factory->make('invoice');
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

// 待操作類別實作後使用
// $invoice = EcPayInvoice::make();
// $query = EcPayQuery::invoice();
```

### 透過容器解析

```php
// 待操作類別實作後使用
// $invoice = app('ecpay-b2b.invoice');
// $client = app('ecpay-b2b.client');
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
├── Infrastructure/              # 基礎設施（加密、編碼）
├── Factories/                   # 工廠模式
├── Laravel/                     # Laravel 整合
├── Operations/                  # 操作類別（待實作）
├── Queries/                     # 查詢類別（待實作）
├── Parameter/                   # 參數常數（待實作）
├── Notifications/               # 通知類別（待實作）
└── Printing/                    # 列印類別（待實作）
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

## 待實作項目

本套件目前僅建立基礎架構，以下模組待依據 [B2B API 規格](https://developers.ecpay.com.tw/?p=14825)實作：

### 前置作業
- [ ] 交易對象維護 (TradingPartner)
- [ ] 查詢財政部配號結果 (GetGovInvoiceWordSetting)
- [ ] 字軌與配號設定 (AddInvoiceWordSetting)
- [ ] 設定字軌號碼狀態 (UpdateInvoiceWordStatus)
- [ ] 查詢字軌 (GetInvoiceWordSetting)

### Operations - 發票作業
- [ ] 開立發票 (Invoice)
- [ ] 開立發票確認 (InvoiceConfirm)
- [ ] 作廢發票 (InvalidInvoice)
- [ ] 作廢發票確認 (InvalidInvoiceConfirm)
- [ ] 退回發票 (RejectInvoice)
- [ ] 退回發票確認 (RejectInvoiceConfirm)
- [ ] 開立折讓發票 (AllowanceInvoice)
- [ ] 折讓發票確認 (AllowanceConfirm)
- [ ] 作廢折讓發票 (InvalidAllowance)
- [ ] 作廢折讓發票確認 (InvalidAllowanceConfirm)

### Queries - 查詢作業
- [ ] 查詢發票 (GetInvoice)
- [ ] 查詢發票確認 (GetInvoiceConfirm)
- [ ] 查詢作廢發票 (GetInvalidInvoice)
- [ ] 查詢作廢發票確認 (GetInvalidInvoiceConfirm)
- [ ] 查詢退回發票 (GetRejectInvoice)
- [ ] 查詢退回發票確認 (GetRejectInvoiceConfirm)
- [ ] 查詢折讓發票 (GetAllowance)
- [ ] 查詢折讓發票確認 (GetAllowanceConfirm)
- [ ] 查詢作廢折讓發票 (GetInvalidAllowance)
- [ ] 查詢作廢折讓發票確認 (GetInvalidAllowanceConfirm)

### Notifications - 發送通知
- [ ] 發送發票通知 (InvoiceNotify)

### Printing - 發票列印
- [ ] 發票列印 (InvoicePrint)
- [ ] 發票列印 PDF (InvoicePrintPdf)

### Parameter - 參數常數
- [ ] 稅別類型 (TaxType)
- [ ] 發票類型 (InvType)
- [ ] 其他 B2B 專用參數

### 其他
- [ ] 完整測試案例
- [ ] 範例程式碼
- [ ] API 文件

## 授權

MIT License

---

# ECPay e-Invoice B2B API Package (English Overview)

This library wraps the official ECPay e-Invoice B2B API. The package structure is ready, but operation classes need to be implemented based on B2B API specifications.

## Parameters

- Server: API endpoint (stage or production)
- MerchantID: merchant code registered with ECPay
- HashKey / HashIV: AES credentials for encrypting `Data`

## Quick Start

```php
$client = new ecPay\eInvoiceB2B\EcPayClient($server, $hashKey, $hashIV);

// Operation classes to be implemented
```

## Module Groups

- `Operations\*`: create/void invoices and allowances (to be implemented)
- `Queries\*`: lookup invoice/allowance status (to be implemented)
- `Notifications\*`: push notifications (to be implemented)
- `Printing\*`: reserved for future printing helpers

All modules extend `Content`, so you can share the same `EcPayClient` to send requests.
