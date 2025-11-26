<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Queries;

use CarlLee\EcPayB2B\Content;
use CarlLee\EcPayB2B\Parameter\B2BInvoiceCategory;
use Exception;

/**
 * 查詢作廢折讓發票確認 API。
 *
 * 特店(營業人)可使用此 API 查詢已作廢發票折讓是否完成確認資訊，
 * 綠界會以回傳參數方式回覆該張作廢折讓發票資料。此方式可協助特店(營業人)將查詢作廢折讓發票確認機制
 * 整合至營業人網站，提供快速查詢服務。
 *
 * @see https://developers.ecpay.com.tw/?p=14983
 */
class GetAllowanceInvalidConfirm extends Content
{
    /**
     * API endpoint path.
     *
     * @var string
     */
    protected string $requestPath = '/B2BInvoice/GetAllowanceInvalidConfirm';

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
            'InvoiceCategory' => '',
            'AllowanceNumber' => '',
            'AllowanceDate' => '',
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
     * 查詢銷項發票折讓（特店開給交易相對人的作廢折讓是否已確認）。
     *
     * @return self
     */
    public function salesInvoice(): self
    {
        return $this->setInvoiceCategory(B2BInvoiceCategory::SALES);
    }

    /**
     * 查詢進項發票折讓（交易相對人開給特店的作廢折讓是否已確認）。
     *
     * @return self
     */
    public function purchaseInvoice(): self
    {
        return $this->setInvoiceCategory(B2BInvoiceCategory::PURCHASE);
    }

    /**
     * 設定折讓號碼。
     *
     * 當自訂編號為空值時，此欄需有值。
     * 折讓號碼為 14 碼，格式為：2碼英文 + 8碼數字 + 4碼流水號。
     *
     * @param string $allowanceNumber
     * @return self
     */
    public function setAllowanceNumber(string $allowanceNumber): self
    {
        $allowanceNumber = strtoupper(trim($allowanceNumber));

        if ($allowanceNumber !== '' && !preg_match('/^[A-Z]{2}\d{8}\d{4}$/', $allowanceNumber)) {
            throw new Exception('AllowanceNumber must be 14 characters (e.g., AB123456780001).');
        }

        $this->content['Data']['AllowanceNumber'] = $allowanceNumber;

        return $this;
    }

    /**
     * 設定折讓開立日期。
     *
     * 當折讓號碼有值時，此欄必填。
     *
     * @param string $allowanceDate 格式為 yyyy-mm-dd
     * @return self
     */
    public function setAllowanceDate(string $allowanceDate): self
    {
        $allowanceDate = trim($allowanceDate);

        if ($allowanceDate !== '') {
            $format = 'Y-m-d';
            $dateTime = \DateTime::createFromFormat($format, $allowanceDate);

            if (!($dateTime && $dateTime->format($format) === $allowanceDate)) {
                throw new Exception('AllowanceDate must be in yyyy-mm-dd format.');
            }
        }

        $this->content['Data']['AllowanceDate'] = $allowanceDate;

        return $this;
    }

    /**
     * 設定自訂編號。
     *
     * 當折讓號碼為空值時，此欄需有值。
     *
     * @param string $relateNumber
     * @return self
     */
    public function setRelateNumber(string $relateNumber): self
    {
        $relateNumber = trim($relateNumber);

        if (strlen($relateNumber) > 20) {
            throw new Exception('RelateNumber cannot exceed 20 characters.');
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
    protected function validation(): void
    {
        $this->validatorBaseParam();

        // 驗證發票種類
        if ($this->content['Data']['InvoiceCategory'] === '') {
            throw new Exception('InvoiceCategory cannot be empty.');
        }

        if (!B2BInvoiceCategory::isValid($this->content['Data']['InvoiceCategory'])) {
            throw new Exception('InvoiceCategory must be 0 (銷項發票) or 1 (進項發票).');
        }

        $allowanceNumber = $this->content['Data']['AllowanceNumber'];
        $relateNumber = $this->content['Data']['RelateNumber'];

        // 折讓號碼與自訂編號至少要有一個
        if (empty($allowanceNumber) && empty($relateNumber)) {
            throw new Exception('Either AllowanceNumber or RelateNumber must be provided.');
        }

        // 當折讓號碼有值時，折讓日期必填
        if (!empty($allowanceNumber) && empty($this->content['Data']['AllowanceDate'])) {
            throw new Exception('AllowanceDate is required when AllowanceNumber is provided.');
        }

        // 驗證折讓號碼格式
        if (!empty($allowanceNumber) && !preg_match('/^[A-Z]{2}\d{8}\d{4}$/', $allowanceNumber)) {
            throw new Exception('AllowanceNumber must be 2 letters followed by 8 digits and 4 digits sequence.');
        }

        // 驗證折讓日期格式
        if (!empty($this->content['Data']['AllowanceDate'])) {
            $format = 'Y-m-d';
            $dateTime = \DateTime::createFromFormat($format, $this->content['Data']['AllowanceDate']);

            if (!($dateTime && $dateTime->format($format) === $this->content['Data']['AllowanceDate'])) {
                throw new Exception('AllowanceDate must be in yyyy-mm-dd format.');
            }
        }
    }
}
