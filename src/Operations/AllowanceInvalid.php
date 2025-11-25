<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Operations;

use CarlLee\EcPayB2B\Content;
use CarlLee\EcPayB2B\Parameter\B2BInvoiceCategory;
use Exception;

/**
 * 作廢折讓發票 API。
 *
 * 交易雙方因發生折讓內容開立錯誤，由特店(營業人)傳送作廢折讓發票參數給綠界科技加值中心
 * (以下簡稱綠界)後，由綠界暫存相關資料。綠界會於隔日將折讓作廢後上傳至財政部電子發票整合服務平台，
 * 同時根據發送通知API設定，通知交易相對人(營業人)電子折讓已作廢。
 *
 * 注意：根據財政部規定，需等待交易相對人(營業人)確認後才完成交換作廢。
 *
 * @see https://developers.ecpay.com.tw/?p=14889
 */
class AllowanceInvalid extends Content
{
    /**
     * API endpoint path.
     *
     * @var string
     */
    protected $requestPath = '/B2BInvoice/AllowanceInvalid';

    /**
     * Initialize request payload.
     *
     * @return void
     */
    protected function initContent()
    {
        $this->content['Data'] = [
            'MerchantID' => $this->merchantID,
            'InvoiceCategory' => B2BInvoiceCategory::SALES,
            'AllowanceNumber' => '',
            'AllowanceDate' => '',
            'InvalidReason' => '',
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
     * 作廢銷項折讓（特店開給交易相對人的折讓）。
     *
     * @return self
     */
    public function salesInvoice(): self
    {
        return $this->setInvoiceCategory(B2BInvoiceCategory::SALES);
    }

    /**
     * 作廢進項折讓（交易相對人開給特店的折讓）。
     *
     * @return self
     */
    public function purchaseInvoice(): self
    {
        return $this->setInvoiceCategory(B2BInvoiceCategory::PURCHASE);
    }

    /**
     * 設定折讓號碼（必填）。
     *
     * 折讓號碼為 14 碼，格式為：2碼英文 + 8碼數字 + 4碼流水號。
     *
     * @param string $allowanceNumber
     * @return self
     */
    public function setAllowanceNumber(string $allowanceNumber): self
    {
        $allowanceNumber = strtoupper(trim($allowanceNumber));

        if (!preg_match('/^[A-Z]{2}\d{8}\d{4}$/', $allowanceNumber)) {
            throw new Exception('AllowanceNumber must be 14 characters (e.g., AB123456780001).');
        }

        $this->content['Data']['AllowanceNumber'] = $allowanceNumber;

        return $this;
    }

    /**
     * 設定折讓開立日期（必填）。
     *
     * @param string $date 格式為 yyyy-mm-dd
     * @return self
     */
    public function setAllowanceDate(string $date): self
    {
        $date = trim($date);
        $format = 'Y-m-d';
        $dateTime = \DateTime::createFromFormat($format, $date);

        if (!($dateTime && $dateTime->format($format) === $date)) {
            throw new Exception('AllowanceDate must be in yyyy-mm-dd format.');
        }

        $this->content['Data']['AllowanceDate'] = $date;

        return $this;
    }

    /**
     * 設定作廢原因（必填）。
     *
     * @param string $reason
     * @return self
     */
    public function setInvalidReason(string $reason): self
    {
        $reason = trim($reason);

        if ($reason === '') {
            throw new Exception('InvalidReason cannot be empty.');
        }

        if (mb_strlen($reason) > 200) {
            throw new Exception('InvalidReason cannot exceed 200 characters.');
        }

        $this->content['Data']['InvalidReason'] = $reason;

        return $this;
    }

    /**
     * 驗證 payload。
     *
     * @return void
     */
    public function validation()
    {
        $this->validatorBaseParam();

        if (!B2BInvoiceCategory::isValid($this->content['Data']['InvoiceCategory'])) {
            throw new Exception('InvoiceCategory must be 0 (銷項發票) or 1 (進項發票).');
        }

        if (empty($this->content['Data']['AllowanceNumber'])) {
            throw new Exception('AllowanceNumber cannot be empty.');
        }

        if (!preg_match('/^[A-Z]{2}\d{8}\d{4}$/', $this->content['Data']['AllowanceNumber'])) {
            throw new Exception('AllowanceNumber must be 14 characters (2 letters + 8 digits + 4 digits).');
        }

        if (empty($this->content['Data']['AllowanceDate'])) {
            throw new Exception('AllowanceDate cannot be empty.');
        }

        $format = 'Y-m-d';
        $dateTime = \DateTime::createFromFormat($format, $this->content['Data']['AllowanceDate']);

        if (!($dateTime && $dateTime->format($format) === $this->content['Data']['AllowanceDate'])) {
            throw new Exception('AllowanceDate must be in yyyy-mm-dd format.');
        }

        if (empty($this->content['Data']['InvalidReason'])) {
            throw new Exception('InvalidReason cannot be empty.');
        }
    }
}

