<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Printing;

use CarlLee\EcPayB2B\Content;
use Exception;

/**
 * 發票列印 PDF API。
 *
 * 特店可使用此 API 取得單一發票 PDF 檔。
 *
 * 注意：同一 IP，10秒內最多只可呼叫 2 次。
 *
 * @see https://developers.ecpay.com.tw/?p=53383
 */
class DownloadB2BPdf extends Content
{
    /**
     * API endpoint path.
     *
     * @var string
     */
    protected string $requestPath = '/B2BInvoice/DownloadB2BPdf';

    /**
     * Initialize request payload.
     *
     * @return void
     */
    #[\Override]
    protected function initContent(): void
    {
        $this->content['Data'] = [
            'MerchantID' => $this->merchantID,
            'InvoiceNumber' => '',
            'InvoiceDate' => '',
        ];
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
     * 驗證 payload。
     *
     * @return void
     */
    #[\Override]
    protected function validation(): void
    {
        $this->validatorBaseParam();

        // 驗證發票號碼
        if (empty($this->content['Data']['InvoiceNumber'])) {
            throw new Exception('InvoiceNumber cannot be empty.');
        }

        if (!preg_match('/^[A-Z]{2}\d{8}$/', $this->content['Data']['InvoiceNumber'])) {
            throw new Exception('InvoiceNumber must be 2 letters followed by 8 digits.');
        }

        // 驗證發票日期
        if (empty($this->content['Data']['InvoiceDate'])) {
            throw new Exception('InvoiceDate cannot be empty.');
        }

        $format = 'Y-m-d';
        $dateTime = \DateTime::createFromFormat($format, $this->content['Data']['InvoiceDate']);

        if (!($dateTime && $dateTime->format($format) === $this->content['Data']['InvoiceDate'])) {
            throw new Exception('InvoiceDate must be in yyyy-mm-dd format.');
        }
    }
}
