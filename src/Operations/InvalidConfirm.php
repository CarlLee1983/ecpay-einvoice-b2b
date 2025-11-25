<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Operations;

use CarlLee\EcPayB2B\Content;
use CarlLee\EcPayB2B\Parameter\ConfirmAction;
use Exception;

/**
 * 作廢發票確認 API。
 *
 * 特店(營業人)收到作廢發票訊息通知後，傳送作廢發票確認參數給綠界科技加值中心(以下簡稱綠界)，
 * 由綠界暫存相關資料。綠界會於隔日將作廢發票確認訊息後上傳至財政部電子發票整合服務平台，
 * 完成發票作廢交換。並根據發送通知API設定，通知交易相對人(營業人)電子發票作廢已完成確認。
 *
 * @see https://developers.ecpay.com.tw/?p=14865
 */
class InvalidConfirm extends Content
{
    /**
     * API endpoint path.
     *
     * @var string
     */
    protected $requestPath = '/B2BInvoice/InvalidConfirm';

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
            'InvoiceNumber' => '',
            'InvoiceDate' => '',
            'ConfirmAction' => ConfirmAction::CONFIRM,
            'RejectReason' => '',
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
     * 設定確認動作（必填）。
     *
     * @param string $action 1: 確認, 2: 退回
     * @return self
     */
    public function setConfirmAction(string $action): self
    {
        if (!ConfirmAction::isValid($action)) {
            throw new Exception('ConfirmAction must be 1 (確認) or 2 (退回).');
        }

        $this->content['Data']['ConfirmAction'] = $action;

        return $this;
    }

    /**
     * 確認作廢。
     *
     * @return self
     */
    public function confirm(): self
    {
        return $this->setConfirmAction(ConfirmAction::CONFIRM);
    }

    /**
     * 退回作廢。
     *
     * @param string $reason 退回原因
     * @return self
     */
    public function reject(string $reason = ''): self
    {
        $this->setConfirmAction(ConfirmAction::REJECT);

        if ($reason !== '') {
            $this->setRejectReason($reason);
        }

        return $this;
    }

    /**
     * 設定退回原因（選填）。
     *
     * 當確認動作為退回時，建議填寫退回原因。
     *
     * @param string $reason
     * @return self
     */
    public function setRejectReason(string $reason): self
    {
        $reason = trim($reason);

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

        if (!ConfirmAction::isValid($this->content['Data']['ConfirmAction'])) {
            throw new Exception('ConfirmAction must be 1 (確認) or 2 (退回).');
        }
    }
}

