<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Notifications;

use CarlLee\EcPayB2B\Content;
use CarlLee\EcPayB2B\Parameter\InvoiceTag;
use CarlLee\EcPayB2B\Parameter\NotifyTarget;
use Exception;

/**
 * 發送發票通知 API。
 *
 * B2B電子發票應在任何發票狀態變動時通知交易雙方，特店(營業人)可使用此API來發送電子發票通知
 * (若不撰寫此 API，則可透過廠商後台功能處理)，綠界將以發票開立時所提供之交易雙方聯絡資料進行通知。
 *
 * 注意：測試環境下綠界不會『主動』發送任何通知，需於廠商管理後台使用『補發通知』，才會寄送通知信到指定信箱。
 *
 * @see https://developers.ecpay.com.tw/?p=14988
 */
class Notify extends Content
{
    /**
     * API endpoint path.
     *
     * @var string
     */
    protected string $requestPath = '/B2BInvoice/Notify';

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
            'InvoiceDate' => '',
            'InvoiceNumber' => '',
            'AllowanceNo' => '',
            'NotifyMail' => '',
            'InvoiceTag' => '',
            'Notified' => NotifyTarget::ALL,
        ];
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
     * 設定折讓單編號（選填）。
     *
     * 當 InvoiceTag 為折讓相關時（4, 5, 9, 10）建議填寫。
     * 長度固定為 16 碼。
     *
     * @param string $allowanceNo
     * @return self
     */
    public function setAllowanceNo(string $allowanceNo): self
    {
        $allowanceNo = trim($allowanceNo);

        if ($allowanceNo !== '' && strlen($allowanceNo) !== 16) {
            throw new Exception('AllowanceNo must be exactly 16 characters.');
        }

        $this->content['Data']['AllowanceNo'] = $allowanceNo;

        return $this;
    }

    /**
     * 設定發送電子郵件（必填）。
     *
     * 可輸入多組，以半形分號(;)區隔。
     *
     * @param string $email
     * @return self
     */
    public function setNotifyMail(string $email): self
    {
        $email = trim($email);

        if ($email === '') {
            throw new Exception('NotifyMail cannot be empty.');
        }

        if (strlen($email) > 200) {
            throw new Exception('NotifyMail cannot exceed 200 characters.');
        }

        // 驗證每個 email 格式
        $emails = explode(';', $email);
        foreach ($emails as $singleEmail) {
            $singleEmail = trim($singleEmail);
            if ($singleEmail !== '' && !filter_var($singleEmail, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('NotifyMail contains invalid email format: ' . $singleEmail);
            }
        }

        $this->content['Data']['NotifyMail'] = $email;

        return $this;
    }

    /**
     * 設定多個發送電子郵件。
     *
     * @param array<string> $emails
     * @return self
     */
    public function setNotifyMails(array $emails): self
    {
        return $this->setNotifyMail(implode(';', $emails));
    }

    /**
     * 設定發送內容類型（必填）。
     *
     * @param string $tag 1-10 代表不同發票狀態通知
     * @return self
     */
    public function setInvoiceTag(string $tag): self
    {
        if (!InvoiceTag::isValid($tag)) {
            throw new Exception('InvoiceTag must be between 1 and 10.');
        }

        $this->content['Data']['InvoiceTag'] = $tag;

        return $this;
    }

    /**
     * 設定為發票開立通知。
     *
     * @return self
     */
    public function issueNotify(): self
    {
        return $this->setInvoiceTag(InvoiceTag::ISSUE);
    }

    /**
     * 設定為發票作廢通知。
     *
     * @return self
     */
    public function invalidNotify(): self
    {
        return $this->setInvoiceTag(InvoiceTag::INVALID);
    }

    /**
     * 設定為發票退回通知。
     *
     * @return self
     */
    public function rejectNotify(): self
    {
        return $this->setInvoiceTag(InvoiceTag::REJECT);
    }

    /**
     * 設定為開立折讓通知。
     *
     * @param string $allowanceNo 折讓單編號（16碼）
     * @return self
     */
    public function allowanceNotify(string $allowanceNo = ''): self
    {
        $this->setInvoiceTag(InvoiceTag::ALLOWANCE);

        if ($allowanceNo !== '') {
            $this->setAllowanceNo($allowanceNo);
        }

        return $this;
    }

    /**
     * 設定為作廢折讓通知。
     *
     * @param string $allowanceNo 折讓單編號（16碼）
     * @return self
     */
    public function allowanceInvalidNotify(string $allowanceNo = ''): self
    {
        $this->setInvoiceTag(InvoiceTag::ALLOWANCE_INVALID);

        if ($allowanceNo !== '') {
            $this->setAllowanceNo($allowanceNo);
        }

        return $this;
    }

    /**
     * 設定為開立發票確認通知。
     *
     * @return self
     */
    public function issueConfirmNotify(): self
    {
        return $this->setInvoiceTag(InvoiceTag::ISSUE_CONFIRM);
    }

    /**
     * 設定為作廢發票確認通知。
     *
     * @return self
     */
    public function invalidConfirmNotify(): self
    {
        return $this->setInvoiceTag(InvoiceTag::INVALID_CONFIRM);
    }

    /**
     * 設定為退回發票確認通知。
     *
     * @return self
     */
    public function rejectConfirmNotify(): self
    {
        return $this->setInvoiceTag(InvoiceTag::REJECT_CONFIRM);
    }

    /**
     * 設定為折讓確認通知。
     *
     * @param string $allowanceNo 折讓單編號（16碼）
     * @return self
     */
    public function allowanceConfirmNotify(string $allowanceNo = ''): self
    {
        $this->setInvoiceTag(InvoiceTag::ALLOWANCE_CONFIRM);

        if ($allowanceNo !== '') {
            $this->setAllowanceNo($allowanceNo);
        }

        return $this;
    }

    /**
     * 設定為作廢折讓確認通知。
     *
     * @param string $allowanceNo 折讓單編號（16碼）
     * @return self
     */
    public function allowanceInvalidConfirmNotify(string $allowanceNo = ''): self
    {
        $this->setInvoiceTag(InvoiceTag::ALLOWANCE_INVALID_CONFIRM);

        if ($allowanceNo !== '') {
            $this->setAllowanceNo($allowanceNo);
        }

        return $this;
    }

    /**
     * 設定發送對象（必填）。
     *
     * @param string $target C: 客戶, M: 特店, A: 皆發送
     * @return self
     */
    public function setNotified(string $target): self
    {
        $target = strtoupper(trim($target));

        if (!NotifyTarget::isValid($target)) {
            throw new Exception('Notified must be C (客戶), M (特店), or A (皆發送).');
        }

        $this->content['Data']['Notified'] = $target;

        return $this;
    }

    /**
     * 發送通知給客戶。
     *
     * @return self
     */
    public function notifyCustomer(): self
    {
        return $this->setNotified(NotifyTarget::CUSTOMER);
    }

    /**
     * 發送通知給合作特店。
     *
     * @return self
     */
    public function notifyMerchant(): self
    {
        return $this->setNotified(NotifyTarget::MERCHANT);
    }

    /**
     * 發送通知給所有人（客戶與特店）。
     *
     * @return self
     */
    public function notifyAll(): self
    {
        return $this->setNotified(NotifyTarget::ALL);
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

        // 驗證發票日期
        if (empty($this->content['Data']['InvoiceDate'])) {
            throw new Exception('InvoiceDate cannot be empty.');
        }

        $format = 'Y-m-d';
        $dateTime = \DateTime::createFromFormat($format, $this->content['Data']['InvoiceDate']);

        if (!($dateTime && $dateTime->format($format) === $this->content['Data']['InvoiceDate'])) {
            throw new Exception('InvoiceDate must be in yyyy-mm-dd format.');
        }

        // 驗證發票號碼
        if (empty($this->content['Data']['InvoiceNumber'])) {
            throw new Exception('InvoiceNumber cannot be empty.');
        }

        if (!preg_match('/^[A-Z]{2}\d{8}$/', $this->content['Data']['InvoiceNumber'])) {
            throw new Exception('InvoiceNumber must be 2 letters followed by 8 digits.');
        }

        // 驗證發送電子郵件
        if (empty($this->content['Data']['NotifyMail'])) {
            throw new Exception('NotifyMail cannot be empty.');
        }

        // 驗證發送內容類型
        if (empty($this->content['Data']['InvoiceTag'])) {
            throw new Exception('InvoiceTag cannot be empty.');
        }

        if (!InvoiceTag::isValid($this->content['Data']['InvoiceTag'])) {
            throw new Exception('InvoiceTag must be between 1 and 10.');
        }

        // 驗證發送對象
        if (!NotifyTarget::isValid($this->content['Data']['Notified'])) {
            throw new Exception('Notified must be C, M, or A.');
        }

        // 驗證折讓單編號（如有填寫）
        $allowanceNo = $this->content['Data']['AllowanceNo'];
        if ($allowanceNo !== '' && strlen($allowanceNo) !== 16) {
            throw new Exception('AllowanceNo must be exactly 16 characters.');
        }
    }
}
