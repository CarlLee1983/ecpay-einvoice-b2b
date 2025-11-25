<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Queries;

use CarlLee\EcPayB2B\Content;
use CarlLee\EcPayB2B\Parameter\B2BInvoiceCategory;
use Exception;

/**
 * 查詢發票確認 API。
 *
 * 特店(營業人)可使用此 API 查詢已開立發票是否完成確認資訊，包括銷項發票及進項發票，
 * 綠界會以回傳參數方式回覆該張發票資料。此方式可協助特店(營業人)將查詢發票確認機制
 * 整合至特店(營業人)網站，提供快速查詢服務。
 *
 * @see https://developers.ecpay.com.tw/?p=14940
 */
class GetIssueConfirm extends Content
{
    /**
     * API endpoint path.
     *
     * @var string
     */
    protected $requestPath = '/B2BInvoice/GetIssueConfirm';

    /**
     * Initialize request payload.
     *
     * @return void
     */
    #[\Override]
    protected function initContent()
    {
        $this->content['Data'] = [
            'MerchantID' => $this->merchantID,
            'InvoiceCategory' => '',
            'InvoiceNumber' => '',
            'InvoiceDate' => '',
            'RelateNumber' => '',
            'Seller_Identifier' => '',
            'Buyer_Identifier' => '',
            'InvoiceDateBegin' => '',
            'InvoiceDateEnd' => '',
            'InvoiceNumberBegin' => '',
            'InvoiceNumberEnd' => '',
            'Issue_Status' => '',
            'Invalid_Status' => '',
            'ExchangeMode' => '',
            'ExchangeStatus' => '',
            'Upload_Status' => '',
        ];
    }

    /**
     * 設定 B2B 發票種類。
     *
     * @param int $category 0: 銷項發票, 1: 進項發票
     * @return self
     */
    public function setInvoiceCategory(int $category): self
    {
        if (!B2BInvoiceCategory::isValid($category)) {
            throw new Exception('InvoiceCategory must be 0 (銷項發票) or 1 (進項發票).');
        }

        $this->content['Data']['InvoiceCategory'] = $category;

        return $this;
    }

    /**
     * 查詢銷項發票（特店開給交易相對人的發票是否已確認）。
     *
     * @return self
     */
    public function salesInvoice(): self
    {
        return $this->setInvoiceCategory(B2BInvoiceCategory::SALES);
    }

    /**
     * 查詢進項發票（交易相對人開給特店的發票是否已確認）。
     *
     * @return self
     */
    public function purchaseInvoice(): self
    {
        return $this->setInvoiceCategory(B2BInvoiceCategory::PURCHASE);
    }

    /**
     * 設定發票號碼。
     *
     * 當自訂編號為空值時，此欄需有值。
     *
     * @param string $invoiceNumber
     * @return self
     */
    public function setInvoiceNumber(string $invoiceNumber): self
    {
        $invoiceNumber = strtoupper(trim($invoiceNumber));

        if ($invoiceNumber !== '' && !preg_match('/^[A-Z]{2}\d{8}$/', $invoiceNumber)) {
            throw new Exception('InvoiceNumber must be 2 letters followed by 8 digits (e.g., AB12345678).');
        }

        $this->content['Data']['InvoiceNumber'] = $invoiceNumber;

        return $this;
    }

    /**
     * 設定發票開立日期。
     *
     * 當發票號碼有值時，此欄必填。
     *
     * @param string $invoiceDate 格式為 yyyy-mm-dd
     * @return self
     */
    public function setInvoiceDate(string $invoiceDate): self
    {
        $invoiceDate = trim($invoiceDate);

        if ($invoiceDate !== '') {
            $this->validateDateFormat($invoiceDate, 'InvoiceDate');
        }

        $this->content['Data']['InvoiceDate'] = $invoiceDate;

        return $this;
    }

    /**
     * 設定自訂編號。
     *
     * 當發票號碼為空值時，此欄需有值。
     *
     * @param string $relateNumber
     * @return self
     */
    public function setRelateNumber(string $relateNumber): self
    {
        $relateNumber = trim($relateNumber);

        if (strlen($relateNumber) > 20) {
            throw new Exception('RelateNumber cannot exceed 20 characters.');
        }

        $this->content['Data']['RelateNumber'] = $relateNumber;

        return $this;
    }

    /**
     * 設定賣家統一編號。
     *
     * @param string $identifier
     * @return self
     */
    public function setSellerIdentifier(string $identifier): self
    {
        $identifier = trim($identifier);

        if ($identifier !== '' && !preg_match('/^\d{8}$/', $identifier)) {
            throw new Exception('Seller_Identifier must be exactly 8 digits.');
        }

        $this->content['Data']['Seller_Identifier'] = $identifier;

        return $this;
    }

    /**
     * 設定買家統一編號。
     *
     * @param string $identifier
     * @return self
     */
    public function setBuyerIdentifier(string $identifier): self
    {
        $identifier = trim($identifier);

        if ($identifier !== '' && !preg_match('/^\d{8}$/', $identifier)) {
            throw new Exception('Buyer_Identifier must be exactly 8 digits.');
        }

        $this->content['Data']['Buyer_Identifier'] = $identifier;

        return $this;
    }

    /**
     * 設定發票開立日期區間。
     *
     * @param string $begin 起始日（yyyy-mm-dd）
     * @param string $end 結束日（yyyy-mm-dd）
     * @return self
     */
    public function setInvoiceDateRange(string $begin, string $end): self
    {
        $begin = trim($begin);
        $end = trim($end);

        if ($begin !== '') {
            $this->validateDateFormat($begin, 'InvoiceDateBegin');
        }

        if ($end !== '') {
            $this->validateDateFormat($end, 'InvoiceDateEnd');
        }

        $this->content['Data']['InvoiceDateBegin'] = $begin;
        $this->content['Data']['InvoiceDateEnd'] = $end;

        return $this;
    }

    /**
     * 設定發票號碼區間（不包含字軌）。
     *
     * @param string $begin 起始號碼（8碼數字）
     * @param string $end 結束號碼（8碼數字）
     * @return self
     */
    public function setInvoiceNumberRange(string $begin, string $end): self
    {
        $begin = trim($begin);
        $end = trim($end);

        if ($begin !== '' && !preg_match('/^\d{8}$/', $begin)) {
            throw new Exception('InvoiceNumberBegin must be exactly 8 digits.');
        }

        if ($end !== '' && !preg_match('/^\d{8}$/', $end)) {
            throw new Exception('InvoiceNumberEnd must be exactly 8 digits.');
        }

        $this->content['Data']['InvoiceNumberBegin'] = $begin;
        $this->content['Data']['InvoiceNumberEnd'] = $end;

        return $this;
    }

    /**
     * 設定發票狀態。
     *
     * @param string $status 0: 發票退回, 1: 發票開立
     * @return self
     */
    public function setIssueStatus(string $status): self
    {
        if ($status !== '' && !in_array($status, ['0', '1'], true)) {
            throw new Exception('Issue_Status must be 0 (退回) or 1 (開立).');
        }

        $this->content['Data']['Issue_Status'] = $status;

        return $this;
    }

    /**
     * 設定作廢狀態。
     *
     * @param string $status 0: 未作廢, 1: 已作廢
     * @return self
     */
    public function setInvalidStatus(string $status): self
    {
        if ($status !== '' && !in_array($status, ['0', '1'], true)) {
            throw new Exception('Invalid_Status must be 0 (未作廢) or 1 (已作廢).');
        }

        $this->content['Data']['Invalid_Status'] = $status;

        return $this;
    }

    /**
     * 設定上傳模式。
     *
     * @param string $mode 0: 存證, 1: 交換
     * @return self
     */
    public function setExchangeMode(string $mode): self
    {
        if ($mode !== '' && !in_array($mode, ['0', '1'], true)) {
            throw new Exception('ExchangeMode must be 0 (存證) or 1 (交換).');
        }

        $this->content['Data']['ExchangeMode'] = $mode;

        return $this;
    }

    /**
     * 設定發票開立交換進度。
     *
     * 當 ExchangeMode=0 時：1: 完成
     * 當 ExchangeMode=1 時：0: 開立等待確認, 1: 接收開立確認
     *
     * @param string $status
     * @return self
     */
    public function setExchangeStatus(string $status): self
    {
        if ($status !== '' && !in_array($status, ['0', '1'], true)) {
            throw new Exception('ExchangeStatus must be 0 or 1.');
        }

        $this->content['Data']['ExchangeStatus'] = $status;

        return $this;
    }

    /**
     * 設定上傳狀態。
     *
     * @param string $status 0: 未上傳, 1: 已上傳, 2: 上傳失敗
     * @return self
     */
    public function setUploadStatus(string $status): self
    {
        if ($status !== '' && !in_array($status, ['0', '1', '2'], true)) {
            throw new Exception('Upload_Status must be 0 (未上傳), 1 (已上傳), or 2 (上傳失敗).');
        }

        $this->content['Data']['Upload_Status'] = $status;

        return $this;
    }

    /**
     * 驗證 payload。
     *
     * @return void
     */
    #[\Override]
    public function validation()
    {
        $this->validatorBaseParam();

        // 驗證發票種類
        if ($this->content['Data']['InvoiceCategory'] === '') {
            throw new Exception('InvoiceCategory cannot be empty.');
        }

        if (!B2BInvoiceCategory::isValid($this->content['Data']['InvoiceCategory'])) {
            throw new Exception('InvoiceCategory must be 0 (銷項發票) or 1 (進項發票).');
        }

        $invoiceNumber = $this->content['Data']['InvoiceNumber'];
        $relateNumber = $this->content['Data']['RelateNumber'];

        // 發票號碼與自訂編號至少要有一個
        if (empty($invoiceNumber) && empty($relateNumber)) {
            throw new Exception('Either InvoiceNumber or RelateNumber must be provided.');
        }

        // 當發票號碼有值時，發票日期必填
        if (!empty($invoiceNumber) && empty($this->content['Data']['InvoiceDate'])) {
            throw new Exception('InvoiceDate is required when InvoiceNumber is provided.');
        }

        // 驗證發票號碼格式
        if (!empty($invoiceNumber) && !preg_match('/^[A-Z]{2}\d{8}$/', $invoiceNumber)) {
            throw new Exception('InvoiceNumber must be 2 letters followed by 8 digits.');
        }

        // 驗證發票日期格式
        if (!empty($this->content['Data']['InvoiceDate'])) {
            $this->validateDateFormat($this->content['Data']['InvoiceDate'], 'InvoiceDate');
        }
    }

    /**
     * 驗證日期格式。
     *
     * @param string $date
     * @param string $fieldName
     * @return void
     */
    private function validateDateFormat(string $date, string $fieldName): void
    {
        $format = 'Y-m-d';
        $dateTime = \DateTime::createFromFormat($format, $date);

        if (!($dateTime && $dateTime->format($format) === $date)) {
            throw new Exception("{$fieldName} must be in yyyy-mm-dd format.");
        }
    }
}

