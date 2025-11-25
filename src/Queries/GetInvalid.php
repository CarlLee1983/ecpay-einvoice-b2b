<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Queries;

use CarlLee\EcPayB2B\Content;
use CarlLee\EcPayB2B\Parameter\B2BInvoiceCategory;
use Exception;

/**
 * 查詢作廢發票 API。
 *
 * 特店(營業人)可使用此 API 查詢已作廢發票資訊，包括銷項發票及進項發票，
 * 綠界會以回傳參數方式回覆該張發票資料。此方式可協助特店(營業人)將查詢發票作廢機制
 * 整合至特店(營業人)網站，提供快速查詢服務。
 *
 * @see https://developers.ecpay.com.tw/?p=14948
 */
class GetInvalid extends Content
{
    /**
     * API endpoint path.
     *
     * @var string
     */
    protected $requestPath = '/B2BInvoice/GetInvalid';

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
     * 查詢銷項發票（特店開給交易相對人的作廢發票）。
     *
     * @return self
     */
    public function salesInvoice(): self
    {
        return $this->setInvoiceCategory(B2BInvoiceCategory::SALES);
    }

    /**
     * 查詢進項發票（交易相對人開給特店的作廢發票）。
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
            $format = 'Y-m-d';
            $dateTime = \DateTime::createFromFormat($format, $invoiceDate);

            if (!($dateTime && $dateTime->format($format) === $invoiceDate)) {
                throw new Exception('InvoiceDate must be in yyyy-mm-dd format.');
            }
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

        if (strlen($relateNumber) > 50) {
            throw new Exception('RelateNumber cannot exceed 50 characters.');
        }

        $this->content['Data']['RelateNumber'] = $relateNumber;

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
            $format = 'Y-m-d';
            $dateTime = \DateTime::createFromFormat($format, $this->content['Data']['InvoiceDate']);

            if (!($dateTime && $dateTime->format($format) === $this->content['Data']['InvoiceDate'])) {
                throw new Exception('InvoiceDate must be in yyyy-mm-dd format.');
            }
        }
    }
}

