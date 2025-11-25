<?php

declare(strict_types=1);

namespace ecPay\eInvoiceB2B\Queries;

use ecPay\eInvoiceB2B\Content;
use ecPay\eInvoiceB2B\Parameter\InvoiceCategory;
use ecPay\eInvoiceB2B\Parameter\InvoiceTerm;
use ecPay\eInvoiceB2B\Parameter\InvType;
use ecPay\eInvoiceB2B\Parameter\UseStatus;
use Exception;

/**
 * 查詢字軌 API。
 *
 * 特店系統可使用此 API 查詢字軌號碼以及字軌的使用情況。
 *
 * @see https://developers.ecpay.com.tw/?p=14845
 */
class GetInvoiceWordSetting extends Content
{
    /**
     * API endpoint path.
     *
     * @var string
     */
    protected $requestPath = '/B2BInvoice/GetInvoiceWordSetting';

    /**
     * Initialize request payload.
     *
     * @return void
     */
    protected function initContent()
    {
        $this->content['Data'] = [
            'MerchantID' => $this->merchantID,
            'InvoiceYear' => '',
            'InvoiceTerm' => InvoiceTerm::ALL,
            'UseStatus' => UseStatus::ALL,
            'InvoiceCategory' => InvoiceCategory::B2B,
            'InvType' => '',
            'InvoiceHeader' => '',
        ];
    }

    /**
     * 設定發票年度（支援西元年或民國年格式）。
     *
     * @param int|string $year
     * @return self
     */
    public function setInvoiceYear(int|string $year): self
    {
        $this->content['Data']['InvoiceYear'] = $this->normalizeInvoiceYear($year);

        return $this;
    }

    /**
     * 設定發票期別（0 代表全部期別）。
     *
     * @param int $term
     * @return self
     */
    public function setInvoiceTerm(int $term): self
    {
        $this->assertInvoiceTerm($term);
        $this->content['Data']['InvoiceTerm'] = $term;

        return $this;
    }

    /**
     * 設定字軌使用狀態（0 代表全部狀態）。
     *
     * @param int $status
     * @return self
     */
    public function setUseStatus(int $status): self
    {
        $this->assertUseStatus($status);
        $this->content['Data']['UseStatus'] = $status;

        return $this;
    }

    /**
     * 設定發票類別（B2B 固定為 2）。
     *
     * @param int $category
     * @return self
     */
    public function setInvoiceCategory(int $category): self
    {
        if ($category !== InvoiceCategory::B2B) {
            throw new Exception('InvoiceCategory must be 2 (B2B).');
        }

        $this->content['Data']['InvoiceCategory'] = $category;

        return $this;
    }

    /**
     * 設定字軌類別。
     *
     * @param string $type
     * @return self
     */
    public function setInvType(string $type): self
    {
        if (!in_array($type, InvType::VALID_TYPES, true)) {
            throw new Exception('InvType only supports 07 (一般稅額) or 08 (特種稅額).');
        }

        $this->content['Data']['InvType'] = $type;

        return $this;
    }

    /**
     * 設定字軌名稱（兩碼英文字母）。
     *
     * @param string $header
     * @return self
     */
    public function setInvoiceHeader(string $header): self
    {
        $header = strtoupper(trim($header));

        if ($header === '') {
            $this->content['Data']['InvoiceHeader'] = '';

            return $this;
        }

        if (!preg_match('/^[A-Z]{2}$/', $header)) {
            throw new Exception('InvoiceHeader must contain exactly two letters.');
        }

        $this->content['Data']['InvoiceHeader'] = $header;

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

        if (empty($this->content['Data']['InvoiceYear'])) {
            throw new Exception('InvoiceYear cannot be empty.');
        }

        $this->assertInvoiceTerm($this->content['Data']['InvoiceTerm']);
        $this->assertUseStatus($this->content['Data']['UseStatus']);

        if ($this->content['Data']['InvoiceCategory'] !== InvoiceCategory::B2B) {
            throw new Exception('InvoiceCategory must be 2 (B2B).');
        }

        if (
            !empty($this->content['Data']['InvType']) &&
            !in_array($this->content['Data']['InvType'], InvType::VALID_TYPES, true)
        ) {
            throw new Exception('InvType only supports 07 (一般稅額) or 08 (特種稅額).');
        }

        if (
            !empty($this->content['Data']['InvoiceHeader']) &&
            !preg_match('/^[A-Z]{2}$/', $this->content['Data']['InvoiceHeader'])
        ) {
            throw new Exception('InvoiceHeader must contain exactly two letters.');
        }
    }

    /**
     * 確保期別在允許範圍內。
     *
     * @param int $term
     * @return void
     */
    private function assertInvoiceTerm(int $term): void
    {
        if (!in_array($term, InvoiceTerm::VALID_TERMS, true)) {
            throw new Exception('InvoiceTerm must be between 0 and 6.');
        }
    }

    /**
     * 確保使用狀態在允許範圍內。
     *
     * @param int $status
     * @return void
     */
    private function assertUseStatus(int $status): void
    {
        if (!in_array($status, UseStatus::VALID_STATUSES, true)) {
            throw new Exception('UseStatus must be between 0 and 6.');
        }
    }

    /**
     * 正規化發票年度為民國年格式。
     *
     * @param int|string $year
     * @return string
     */
    private function normalizeInvoiceYear(int|string $year): string
    {
        if (is_int($year)) {
            $year = (string) $year;
        }

        $year = trim($year);

        if ($year === '') {
            throw new Exception('InvoiceYear cannot be empty.');
        }

        if (!ctype_digit($year)) {
            throw new Exception('InvoiceYear must be numeric.');
        }

        // 如果是 4 碼，視為西元年，轉換為民國年
        if (strlen($year) === 4) {
            $converted = (int) $year - 1911;

            if ($converted <= 0) {
                throw new Exception('Gregorian year must be greater than 1911.');
            }

            $year = (string) $converted;
        }

        if (strlen($year) > 3) {
            throw new Exception('InvoiceYear must be 3 digits in ROC format.');
        }

        $yearValue = (int) $year;

        // 僅可查詢去年、當年與明年的發票年度
        $current = (int) date('Y') - 1911;
        $min = $current - 1;
        $max = $current + 1;

        if ($yearValue < $min || $yearValue > $max) {
            throw new Exception('InvoiceYear can only target last, current, or next year.');
        }

        return str_pad((string) $yearValue, 3, '0', STR_PAD_LEFT);
    }
}
