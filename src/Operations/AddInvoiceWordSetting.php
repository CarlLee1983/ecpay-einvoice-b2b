<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Operations;

use CarlLee\EcPayB2B\Content;
use CarlLee\EcPayB2B\Parameter\InvoiceCategory;
use CarlLee\EcPayB2B\Parameter\InvoiceTerm;
use CarlLee\EcPayB2B\Parameter\InvType;
use Exception;

/**
 * 字軌與配號設定 API。
 *
 * 當營業人(特店)取得財政部的配號結果後，可建立當年度(含當月)或下個年度的字軌。
 * 在開立發票之前，必須先設定字軌區間，並且可設定多組。
 *
 * 注意：在新增字軌前須自行檢核字軌正確性。
 * 注意：新增字軌後，字軌狀態預設為已審核通過但未啟用，請使用設定字軌號碼狀態 API 進行啟用。
 *
 * @see https://developers.ecpay.com.tw/?p=14835
 */
class AddInvoiceWordSetting extends Content
{
    /**
     * API endpoint path.
     *
     * @var string
     */
    protected $requestPath = '/B2BInvoice/AddInvoiceWordSetting';

    /**
     * 新增字軌時允許的期別（不含 0 全部）。
     */
    private const ALLOWED_TERMS = [
        InvoiceTerm::JAN_FEB,
        InvoiceTerm::MAR_APR,
        InvoiceTerm::MAY_JUN,
        InvoiceTerm::JUL_AUG,
        InvoiceTerm::SEP_OCT,
        InvoiceTerm::NOV_DEC,
    ];

    /**
     * Initialize request payload.
     *
     * @return void
     */
    protected function initContent()
    {
        $this->content['Data'] = [
            'MerchantID' => $this->merchantID,
            'InvoiceTerm' => '',
            'InvoiceYear' => '',
            'InvType' => InvType::GENERAL,
            'InvoiceCategory' => (string) InvoiceCategory::B2B->value,
            'InvoiceHeader' => '',
            'InvoiceStart' => '',
            'InvoiceEnd' => '',
        ];
    }

    /**
     * 設定發票期別。
     *
     * @param int $term 1: 1-2月, 2: 3-4月, 3: 5-6月, 4: 7-8月, 5: 9-10月, 6: 11-12月
     * @return self
     */
    public function setInvoiceTerm(int $term): self
    {
        $this->assertInvoiceTerm($term);
        $this->content['Data']['InvoiceTerm'] = $term;

        return $this;
    }

    /**
     * 設定發票年度（支援西元年或民國年格式）。
     *
     * 僅可設定當年與明年。
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
     * 設定字軌類別。
     *
     * @param string $type 07: 一般稅額發票, 08: 特種稅額發票
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
     * 設定發票種類（B2B 固定為 2）。
     *
     * @param int $category
     * @return self
     */
    public function setInvoiceCategory(int $category): self
    {
        if ($category !== InvoiceCategory::B2B->value) {
            throw new Exception('InvoiceCategory must be 2 (B2B).');
        }

        $this->content['Data']['InvoiceCategory'] = (string) $category;

        return $this;
    }

    /**
     * 設定發票字軌（兩碼英文字母）。
     *
     * @param string $header
     * @return self
     */
    public function setInvoiceHeader(string $header): self
    {
        $header = strtoupper(trim($header));

        if (!preg_match('/^[A-Z]{2}$/', $header)) {
            throw new Exception('InvoiceHeader must contain exactly two letters.');
        }

        $this->content['Data']['InvoiceHeader'] = $header;

        return $this;
    }

    /**
     * 設定起始發票號碼。
     *
     * 請輸入 8 碼發票號碼，尾數需為 00 或 50。
     *
     * @param string $invoiceStart
     * @return self
     */
    public function setInvoiceStart(string $invoiceStart): self
    {
        $invoiceStart = trim($invoiceStart);

        if (!preg_match('/^\d{8}$/', $invoiceStart)) {
            throw new Exception('InvoiceStart must be exactly 8 digits.');
        }

        $lastTwo = substr($invoiceStart, -2);
        if ($lastTwo !== '00' && $lastTwo !== '50') {
            throw new Exception('InvoiceStart must end with 00 or 50.');
        }

        $this->content['Data']['InvoiceStart'] = $invoiceStart;

        return $this;
    }

    /**
     * 設定結束發票號碼。
     *
     * 請輸入 8 碼發票號碼，尾數需為 49 或 99。
     *
     * @param string $invoiceEnd
     * @return self
     */
    public function setInvoiceEnd(string $invoiceEnd): self
    {
        $invoiceEnd = trim($invoiceEnd);

        if (!preg_match('/^\d{8}$/', $invoiceEnd)) {
            throw new Exception('InvoiceEnd must be exactly 8 digits.');
        }

        $lastTwo = substr($invoiceEnd, -2);
        if ($lastTwo !== '49' && $lastTwo !== '99') {
            throw new Exception('InvoiceEnd must end with 49 or 99.');
        }

        $this->content['Data']['InvoiceEnd'] = $invoiceEnd;

        return $this;
    }

    /**
     * 設定發票號碼區間。
     *
     * @param string $start 起始號碼（尾數需為 00 或 50）
     * @param string $end 結束號碼（尾數需為 49 或 99）
     * @return self
     */
    public function setInvoiceRange(string $start, string $end): self
    {
        $this->setInvoiceStart($start);
        $this->setInvoiceEnd($end);

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

        // 驗證發票期別
        if (empty($this->content['Data']['InvoiceTerm'])) {
            throw new Exception('InvoiceTerm cannot be empty.');
        }
        $this->assertInvoiceTerm($this->content['Data']['InvoiceTerm']);

        // 驗證發票年度
        if (empty($this->content['Data']['InvoiceYear'])) {
            throw new Exception('InvoiceYear cannot be empty.');
        }

        // 驗證字軌類別
        if (!in_array($this->content['Data']['InvType'], InvType::VALID_TYPES, true)) {
            throw new Exception('InvType only supports 07 (一般稅額) or 08 (特種稅額).');
        }

        // 驗證發票種類
        if ($this->content['Data']['InvoiceCategory'] !== (string) InvoiceCategory::B2B->value) {
            throw new Exception('InvoiceCategory must be 2 (B2B).');
        }

        // 驗證發票字軌
        if (empty($this->content['Data']['InvoiceHeader'])) {
            throw new Exception('InvoiceHeader cannot be empty.');
        }

        if (!preg_match('/^[A-Z]{2}$/', $this->content['Data']['InvoiceHeader'])) {
            throw new Exception('InvoiceHeader must contain exactly two letters.');
        }

        // 驗證起始發票號碼
        $this->validateInvoiceStart($this->content['Data']['InvoiceStart']);

        // 驗證結束發票號碼
        $this->validateInvoiceEnd($this->content['Data']['InvoiceEnd']);

        // 驗證號碼區間邏輯
        $this->validateInvoiceRange(
            $this->content['Data']['InvoiceStart'],
            $this->content['Data']['InvoiceEnd']
        );
    }

    /**
     * 確保期別在允許範圍內（新增時不可使用 0 全部）。
     *
     * @param int $term
     * @return void
     */
    private function assertInvoiceTerm(int $term): void
    {
        if (!in_array($term, self::ALLOWED_TERMS, true)) {
            throw new Exception('InvoiceTerm must be between 1 and 6.');
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

        // 僅可設定當年與明年
        $current = (int) date('Y') - 1911;
        $max = $current + 1;

        if ($yearValue < $current || $yearValue > $max) {
            throw new Exception('InvoiceYear can only be current or next year.');
        }

        return str_pad((string) $yearValue, 3, '0', STR_PAD_LEFT);
    }

    /**
     * 驗證起始發票號碼。
     *
     * @param string $invoiceStart
     * @return void
     */
    private function validateInvoiceStart(string $invoiceStart): void
    {
        if (empty($invoiceStart)) {
            throw new Exception('InvoiceStart cannot be empty.');
        }

        if (!preg_match('/^\d{8}$/', $invoiceStart)) {
            throw new Exception('InvoiceStart must be exactly 8 digits.');
        }

        $lastTwo = substr($invoiceStart, -2);
        if ($lastTwo !== '00' && $lastTwo !== '50') {
            throw new Exception('InvoiceStart must end with 00 or 50.');
        }
    }

    /**
     * 驗證結束發票號碼。
     *
     * @param string $invoiceEnd
     * @return void
     */
    private function validateInvoiceEnd(string $invoiceEnd): void
    {
        if (empty($invoiceEnd)) {
            throw new Exception('InvoiceEnd cannot be empty.');
        }

        if (!preg_match('/^\d{8}$/', $invoiceEnd)) {
            throw new Exception('InvoiceEnd must be exactly 8 digits.');
        }

        $lastTwo = substr($invoiceEnd, -2);
        if ($lastTwo !== '49' && $lastTwo !== '99') {
            throw new Exception('InvoiceEnd must end with 49 or 99.');
        }
    }

    /**
     * 驗證發票號碼區間邏輯。
     *
     * @param string $start
     * @param string $end
     * @return void
     */
    private function validateInvoiceRange(string $start, string $end): void
    {
        $startNum = (int) $start;
        $endNum = (int) $end;

        if ($endNum <= $startNum) {
            throw new Exception('InvoiceEnd must be greater than InvoiceStart.');
        }

        // 驗證區間配對（00-49 或 50-99）
        $startLastTwo = substr($start, -2);
        $endLastTwo = substr($end, -2);

        $validPairs = [
            '00' => '49',
            '50' => '99',
        ];

        if (!isset($validPairs[$startLastTwo]) || $validPairs[$startLastTwo] !== $endLastTwo) {
            throw new Exception('Invoice range must be 00-49 or 50-99 pair.');
        }
    }
}

