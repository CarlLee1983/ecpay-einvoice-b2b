<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Operations;

use CarlLee\EcPayB2B\Content;
use CarlLee\EcPayB2B\Parameter\B2BInvoiceCategory;
use Exception;

/**
 * 退回發票 API。
 *
 * 交易雙方因發生銷貨退回或發票內容開立錯誤，由特店(營業人)傳送退回發票參數給綠界科技加值中心
 * (以下簡稱綠界)後，由綠界暫存相關資料。綠界會於隔日將發票退回後上傳至財政部電子發票整合服務平台，
 * 同時根據發送通知API設定，通知交易相對人(營業人)電子發票已退回。
 *
 * 注意：根據財政部規定，需等待交易相對人(營業人)確認後才完成交換退回。
 *
 * @see https://developers.ecpay.com.tw/?p=14870
 */
class Reject extends Content
{
    /**
     * API endpoint path.
     *
     * @var string
     */
    protected $requestPath = '/B2BInvoice/Reject';

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
            'InvoiceCategory' => B2BInvoiceCategory::SALES,
            'InvoiceNumber' => '',
            'InvoiceDate' => '',
            'RejectReason' => '',
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
     * 退回銷項發票（特店開給交易相對人的發票）。
     *
     * @return self
     */
    public function salesInvoice(): self
    {
        return $this->setInvoiceCategory(B2BInvoiceCategory::SALES);
    }

    /**
     * 退回進項發票（交易相對人開給特店的發票）。
     *
     * @return self
     */
    public function purchaseInvoice(): self
    {
        return $this->setInvoiceCategory(B2BInvoiceCategory::PURCHASE);
    }

    /**
     * 設定發票號碼（必填）。
     *
     * @param string $invoiceNumber 發票號碼（10碼：2碼英文 + 8碼數字）
     * @return self
     */
    public function setInvoiceNumber(string $invoiceNumber): self
    {
        $invoiceNumber = strtoupper(trim($invoiceNumber));

        if (!preg_match('/^[A-Z]{2}\d{8}$/', $invoiceNumber)) {
            throw new Exception('InvoiceNumber must be 2 letters followed by 8 digits (e.g., AB12345678).');
        }

        $this->content['Data']['InvoiceNumber'] = $invoiceNumber;

        return $this;
    }

    /**
     * 設定發票開立日期（必填）。
     *
     * @param string $date 格式為 yyyy-mm-dd
     * @return self
     */
    public function setInvoiceDate(string $date): self
    {
        $date = trim($date);
        $format = 'Y-m-d';
        $dateTime = \DateTime::createFromFormat($format, $date);

        if (!($dateTime && $dateTime->format($format) === $date)) {
            throw new Exception('InvoiceDate must be in yyyy-mm-dd format.');
        }

        $this->content['Data']['InvoiceDate'] = $date;

        return $this;
    }

    /**
     * 設定退回原因（必填）。
     *
     * @param string $reason
     * @return self
     */
    public function setRejectReason(string $reason): self
    {
        $reason = trim($reason);

        if ($reason === '') {
            throw new Exception('RejectReason cannot be empty.');
        }

        if (mb_strlen($reason) > 200) {
            throw new Exception('RejectReason cannot exceed 200 characters.');
        }

        $this->content['Data']['RejectReason'] = $reason;

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

        if (!B2BInvoiceCategory::isValid($this->content['Data']['InvoiceCategory'])) {
            throw new Exception('InvoiceCategory must be 0 (銷項發票) or 1 (進項發票).');
        }

        if (empty($this->content['Data']['InvoiceNumber'])) {
            throw new Exception('InvoiceNumber cannot be empty.');
        }

        if (!preg_match('/^[A-Z]{2}\d{8}$/', $this->content['Data']['InvoiceNumber'])) {
            throw new Exception('InvoiceNumber must be 2 letters followed by 8 digits.');
        }

        if (empty($this->content['Data']['InvoiceDate'])) {
            throw new Exception('InvoiceDate cannot be empty.');
        }

        $format = 'Y-m-d';
        $dateTime = \DateTime::createFromFormat($format, $this->content['Data']['InvoiceDate']);

        if (!($dateTime && $dateTime->format($format) === $this->content['Data']['InvoiceDate'])) {
            throw new Exception('InvoiceDate must be in yyyy-mm-dd format.');
        }

        if (empty($this->content['Data']['RejectReason'])) {
            throw new Exception('RejectReason cannot be empty.');
        }
    }
}
