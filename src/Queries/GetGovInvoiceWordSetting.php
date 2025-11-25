<?php

declare(strict_types=1);

namespace ecPay\eInvoiceB2B\Queries;

use ecPay\eInvoiceB2B\Content;
use Exception;

/**
 * 查詢財政部配號結果 API。
 *
 * 特店可透過 API 查詢財政部整合服務平台授權於綠界之發票號碼配號結果。
 *
 * 注意：如查無資料，可能的原因為取字軌號碼時並未授權於綠界，或字軌尚未取號完成。
 *
 * @see https://developers.ecpay.com.tw/?p=25206
 */
class GetGovInvoiceWordSetting extends Content
{
    /**
     * API endpoint path.
     *
     * @var string
     */
    protected $requestPath = '/B2BInvoice/GetGovInvoiceWordSetting';

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
        ];
    }

    /**
     * 設定發票年度（支援西元年或民國年格式）。
     *
     * 僅可查詢去年、當年與明年的發票年度。
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

