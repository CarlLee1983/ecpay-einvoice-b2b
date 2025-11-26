# 變更日誌

所有關於此專案的重要變更都會記錄在此文件中。

格式基於 [Keep a Changelog](https://keepachangelog.com/zh-TW/1.1.0/)，
並遵循 [語意化版本](https://semver.org/lang/zh-TW/)。

## [Unreleased]

---

## [1.1.0] - 2025-11-26

### 新增
- **自訂例外類別層級**
  - `EcPayException` - 基礎例外類別，包含 context 功能
  - `ValidationException` - 參數驗證失敗例外（含靜態工廠方法）
  - `EncryptionException` - AES 加解密失敗例外（含靜態工廠方法）
  - `ApiException` - API 請求/回應錯誤例外
  - `PayloadException` - Payload 結構/資料無效例外
  - `ConfigurationException` - 設定相關例外
- **Response 類別新增便利方法**
  - `isSuccess()` - `success()` 的別名
  - `isError()` - 檢查是否為錯誤回應
  - `getCode()` - 取得回應代碼（RtnCode）
  - `toArray()` - 轉換為陣列
  - `throw()` - 若為錯誤則拋出 `ApiException`
- **測試結構改善**
  - 新增 `test/Unit/UnitTestCase.php` 單元測試基類
  - 新增 `test/Integration/IntegrationTestCase.php` 整合測試基類
  - 新增 `test/Laravel/` 目錄

### 變更
- **測試檔案加入 namespace** - 所有測試類別現在位於 `CarlLee\EcPayB2B\Tests` 命名空間
- **統一 PHP 8.3 現代語法**
  - 所有屬性使用 typed properties
  - Infrastructure 類別使用 constructor property promotion
  - 實作介面方法加入 `#[\Override]` 屬性
- **更新 composer.json**
  - 新增 `autoload-dev` 設定
  - 新增測試腳本：`test:unit`, `test:integration`, `test:legacy`, `test:all`
- **更新 phpunit.xml**
  - 新增 unit/integration/legacy/all 測試套件
  - 加入 PHPUnit 10 現代設定選項
- **PayloadEncoder 使用 PHP 8.3 json_validate()** - 改善 JSON 驗證效能

### 修正
- **修正 OperationFactory BASE_NAMESPACE** - 修正工廠類別的命名空間常數為正確的 `CarlLee\EcPayB2B`

### 內部改進
- 核心類別更新使用自訂例外
  - `CipherService` → 使用 `EncryptionException`
  - `PayloadEncoder` → 使用 `PayloadException`, `ApiException`
  - `Request` → 使用 `ApiException`
  - `Content` → 使用 `ValidationException`, `EncryptionException`
  - `AES` trait → 使用 `EncryptionException`
- 與 B2C 專案架構同步，提升程式碼一致性

---

## [1.0.0] - 2024-01-15

### 新增
- **完整實作 27 個 B2B 電子發票 API**
  - 前置作業 API（4 個）
    - `MaintainMerchantCustomerData` - 交易對象維護
    - `GetGovInvoiceWordSetting` - 查詢財政部配號結果
    - `AddInvoiceWordSetting` - 字軌與配號設定
    - `UpdateInvoiceWordStatus` - 設定字軌號碼狀態
  - 發票作業 API（10 個）
    - `Issue` - 開立發票
    - `IssueConfirm` - 開立發票確認
    - `Invalid` - 作廢發票
    - `InvalidConfirm` - 作廢發票確認
    - `Reject` - 退回發票
    - `RejectConfirm` - 退回發票確認
    - `Allowance` - 開立折讓發票
    - `AllowanceConfirm` - 折讓發票確認
    - `AllowanceInvalid` - 作廢折讓發票
    - `AllowanceInvalidConfirm` - 作廢折讓發票確認
  - 查詢作業 API（10 個）
    - `GetIssue` - 查詢發票
    - `GetIssueConfirm` - 查詢發票確認
    - `GetInvalid` - 查詢作廢發票
    - `GetInvalidConfirm` - 查詢作廢發票確認
    - `GetReject` - 查詢退回發票
    - `GetRejectConfirm` - 查詢退回發票確認
    - `GetAllowance` - 查詢折讓發票
    - `GetAllowanceConfirm` - 查詢折讓發票確認
    - `GetAllowanceInvalid` - 查詢作廢折讓發票
    - `GetAllowanceInvalidConfirm` - 查詢作廢折讓發票確認
  - 通知與列印 API（3 個）
    - `Notify` - 發送發票通知
    - `InvoicePrint` - 發票列印（取得列印網址）
    - `DownloadB2BPdf` - 發票列印 PDF

- **類型安全的參數常數類別（PHP 8.1 Backed Enum）**
  - `TaxType` - 課稅類別
  - `ZeroTaxRate` - 零稅率類型
  - `SpecialTaxType` - 特種稅額類別
  - `InvType` - 發票類型
  - `B2BInvoiceCategory` - 發票類別
  - `ExchangeMode` - 交換模式
  - `ConfirmAction` - 確認動作
  - `InvalidReason` - 作廢原因
  - `InvoiceTag` - 發送內容類型
  - `NotifyTarget` - 發送對象
  - `CustomerType` - 交易對象類型
  - `MaintainAction` - 維護動作
  - `InvoiceWordStatus` - 字軌狀態
  - `InvoiceCategory` - 發票種類
  - `InvoiceTerm` - 發票期別
  - `UseStatus` - 使用狀態

- **DTO（資料傳輸物件）**
  - `InvoiceItemDto` - 發票商品項目
  - `AllowanceItemDto` - 折讓商品項目
  - `ItemCollection` - 商品項目集合
  - `RqHeaderDto` - 請求標頭

- **基礎設施**
  - `CipherService` - AES 加解密服務
  - `PayloadEncoder` - Payload 編碼器
  - `OperationFactory` - 操作工廠

- **Laravel 整合**
  - `EcPayServiceProvider` - 服務提供者
  - `EcPayInvoice` Facade
  - `EcPayQuery` Facade
  - 設定檔發布支援

- **測試覆蓋**
  - 484 個單元測試
  - 785 個斷言

### 技術規格
- 最低 PHP 版本：8.3
- 依賴：Guzzle HTTP ^7.0
- 支援 TLS 1.1 以上加密通訊協定
- 遵循 PSR-12 編碼規範

---

## [0.1.0] - 2024-01-01

### 新增
- 初始版本
- 基礎架構建立
- 查詢字軌 API (`GetInvoiceWordSetting`)

---

[Unreleased]: https://github.com/CarlLee1983/ecpay-einvoice-B2B/compare/v1.1.0...HEAD
[1.1.0]: https://github.com/CarlLee1983/ecpay-einvoice-B2B/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/CarlLee1983/ecpay-einvoice-B2B/compare/v0.1.0...v1.0.0
[0.1.0]: https://github.com/CarlLee1983/ecpay-einvoice-B2B/releases/tag/v0.1.0

