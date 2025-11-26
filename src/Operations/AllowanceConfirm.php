<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Operations;

use CarlLee\EcPayB2B\Content;
use CarlLee\EcPayB2B\Parameter\ConfirmAction;
use Exception;

/**
 * 折讓發票確認 API。
 *
 * 特店(營業人)收到折讓發票訊息通知後，傳送折讓發票確認參數給綠界科技加值中心(以下簡稱綠界)，
 * 由綠界暫存相關資料。綠界會於隔日將折讓發票確認訊息後上傳至財政部電子發票整合服務平台，
 * 完成折讓交換。並根據發送通知API設定，通知交易相對人(營業人)電子折讓已完成確認。
 *
 * @see https://developers.ecpay.com.tw/?p=14880
 */
class AllowanceConfirm extends Content
{
    /**
     * API endpoint path.
     *
     * @var string
     */
    protected string $requestPath = '/B2BInvoice/AllowanceConfirm';

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
            'AllowanceNumber' => '',
            'AllowanceDate' => '',
            'ConfirmAction' => ConfirmAction::CONFIRM,
            'RejectReason' => '',
        ];
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
     * 確認折讓。
     *
     * @return self
     */
    public function confirm(): self
    {
        return $this->setConfirmAction(ConfirmAction::CONFIRM);
    }

    /**
     * 退回折讓。
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

        if (!ConfirmAction::isValid($this->content['Data']['ConfirmAction'])) {
            throw new Exception('ConfirmAction must be 1 (確認) or 2 (退回).');
        }
    }
}
