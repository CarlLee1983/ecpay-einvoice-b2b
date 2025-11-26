<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Operations;

use CarlLee\EcPayB2B\Content;
use CarlLee\EcPayB2B\DTO\InvoiceItemDto;
use CarlLee\EcPayB2B\DTO\ItemCollection;
use CarlLee\EcPayB2B\Parameter\ExchangeMode;
use CarlLee\EcPayB2B\Parameter\InvType;
use CarlLee\EcPayB2B\Parameter\SpecialTaxType;
use CarlLee\EcPayB2B\Parameter\TaxType;
use CarlLee\EcPayB2B\Parameter\ZeroTaxRate;
use Exception;

/**
 * 開立發票 API。
 *
 * 特店(營業人)傳送開立發票參數給綠界科技加值中心(以下簡稱綠界)後，
 * 由綠界暫存相關資料。綠界會於隔日開立發票後上傳至財政部電子發票整合服務平台，
 * 並根據發送通知API設定，通知交易相對人(營業人)電子發票已開立。
 *
 * @see https://developers.ecpay.com.tw/?p=14850
 */
class Issue extends Content
{
    /**
     * API endpoint path.
     *
     * @var string
     */
    protected string $requestPath = '/B2BInvoice/Issue';

    /**
     * 商品集合。
     *
     * @var ItemCollection
     */
    protected ItemCollection $items;

    /**
     * Initialize request payload.
     *
     * @return void
     */
    #[\Override]
    protected function initContent()
    {
        $this->items = new ItemCollection();

        $this->content['Data'] = [
            'MerchantID' => $this->merchantID,
            'RelateNumber' => '',
            'InvoiceDate' => '',
            'InvType' => InvType::GENERAL,
            'ExchangeMode' => ExchangeMode::EXCHANGE,
            'Buyer_Identifier' => '',
            'Buyer_Name' => '',
            'Buyer_Address' => '',
            'Buyer_TelephoneNumber' => '',
            'Buyer_FacsimileNumber' => '',
            'Buyer_EmailAddress' => '',
            'Buyer_CustomerNumber' => '',
            'Buyer_RoleRemark' => '',
            'Seller_CustomerNumber' => '',
            'Seller_RoleRemark' => '',
            'TaxType' => TaxType::TAXABLE,
            'ZeroTaxRateReason' => '',
            'SpecialTaxType' => '',
            'TaxRate' => '',
            'SalesAmount' => 0,
            'TaxAmount' => 0,
            'TotalAmount' => 0,
            'MainRemark' => '',
            'Items' => [],
        ];
    }

    /**
     * 設定自訂編號（必填）。
     *
     * 均為唯一值不可重覆使用，請勿使用特殊符號。
     * 大小寫英文視為相同。
     *
     * @param string $relateNumber
     * @return self
     */
    public function setRelateNumber(string $relateNumber): self
    {
        $relateNumber = trim($relateNumber);

        if (strlen($relateNumber) > 30) {
            throw new Exception('RelateNumber cannot exceed 30 characters.');
        }

        if (preg_match('/[^a-zA-Z0-9]/', $relateNumber)) {
            throw new Exception('RelateNumber should not contain special characters.');
        }

        $this->content['Data']['RelateNumber'] = $relateNumber;

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
     * 設定字軌類別（必填）。
     *
     * @param string $type 07: 一般稅額發票, 08: 特種稅額發票
     * @return self
     */
    public function setInvType(string $type): self
    {
        if (!in_array($type, InvType::VALID_TYPES, true)) {
            throw new Exception('InvType must be 07 (一般稅額) or 08 (特種稅額).');
        }

        $this->content['Data']['InvType'] = $type;

        return $this;
    }

    /**
     * 設定為一般稅額發票。
     *
     * @return self
     */
    public function generalInvoice(): self
    {
        return $this->setInvType(InvType::GENERAL);
    }

    /**
     * 設定為特種稅額發票。
     *
     * @return self
     */
    public function specialInvoice(): self
    {
        return $this->setInvType(InvType::SPECIAL);
    }

    /**
     * 設定開立形式（必填）。
     *
     * @param string $mode 0: 存證, 1: 交換
     * @return self
     */
    public function setExchangeMode(string $mode): self
    {
        if (!ExchangeMode::isValid($mode)) {
            throw new Exception('ExchangeMode must be 0 (存證) or 1 (交換).');
        }

        $this->content['Data']['ExchangeMode'] = $mode;

        return $this;
    }

    /**
     * 設定為存證模式。
     *
     * @return self
     */
    public function archiveMode(): self
    {
        return $this->setExchangeMode(ExchangeMode::ARCHIVE);
    }

    /**
     * 設定為交換模式。
     *
     * @return self
     */
    public function exchangeMode(): self
    {
        return $this->setExchangeMode(ExchangeMode::EXCHANGE);
    }

    /**
     * 設定買方統一編號（必填）。
     *
     * @param string $identifier 8 碼統一編號
     * @return self
     */
    public function setBuyerIdentifier(string $identifier): self
    {
        $identifier = trim($identifier);

        if (!preg_match('/^\d{8}$/', $identifier)) {
            throw new Exception('Buyer_Identifier must be 8 digits.');
        }

        $this->content['Data']['Buyer_Identifier'] = $identifier;

        return $this;
    }

    /**
     * 設定買方名稱（必填）。
     *
     * @param string $name
     * @return self
     */
    public function setBuyerName(string $name): self
    {
        $name = trim($name);

        if ($name === '') {
            throw new Exception('Buyer_Name cannot be empty.');
        }

        if (mb_strlen($name) > 60) {
            throw new Exception('Buyer_Name cannot exceed 60 characters.');
        }

        $this->content['Data']['Buyer_Name'] = $name;

        return $this;
    }

    /**
     * 設定買方地址（選填）。
     *
     * @param string $address
     * @return self
     */
    public function setBuyerAddress(string $address): self
    {
        $address = trim($address);

        if (mb_strlen($address) > 100) {
            throw new Exception('Buyer_Address cannot exceed 100 characters.');
        }

        $this->content['Data']['Buyer_Address'] = $address;

        return $this;
    }

    /**
     * 設定買方電話（選填）。
     *
     * @param string $tel
     * @return self
     */
    public function setBuyerTelephoneNumber(string $tel): self
    {
        $tel = trim($tel);

        if (strlen($tel) > 26) {
            throw new Exception('Buyer_TelephoneNumber cannot exceed 26 characters.');
        }

        $this->content['Data']['Buyer_TelephoneNumber'] = $tel;

        return $this;
    }

    /**
     * 設定買方傳真（選填）。
     *
     * @param string $fax
     * @return self
     */
    public function setBuyerFacsimileNumber(string $fax): self
    {
        $fax = trim($fax);

        if (strlen($fax) > 26) {
            throw new Exception('Buyer_FacsimileNumber cannot exceed 26 characters.');
        }

        $this->content['Data']['Buyer_FacsimileNumber'] = $fax;

        return $this;
    }

    /**
     * 設定買方 Email（選填）。
     *
     * @param string $email
     * @return self
     */
    public function setBuyerEmailAddress(string $email): self
    {
        $email = trim($email);

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Buyer_EmailAddress format is invalid.');
        }

        if (strlen($email) > 80) {
            throw new Exception('Buyer_EmailAddress cannot exceed 80 characters.');
        }

        $this->content['Data']['Buyer_EmailAddress'] = $email;

        return $this;
    }

    /**
     * 設定買方客戶編號（選填）。
     *
     * @param string $customerNumber
     * @return self
     */
    public function setBuyerCustomerNumber(string $customerNumber): self
    {
        $customerNumber = trim($customerNumber);

        if (strlen($customerNumber) > 20) {
            throw new Exception('Buyer_CustomerNumber cannot exceed 20 characters.');
        }

        $this->content['Data']['Buyer_CustomerNumber'] = $customerNumber;

        return $this;
    }

    /**
     * 設定買方角色註記（選填）。
     *
     * @param string $remark
     * @return self
     */
    public function setBuyerRoleRemark(string $remark): self
    {
        $remark = trim($remark);

        if (mb_strlen($remark) > 40) {
            throw new Exception('Buyer_RoleRemark cannot exceed 40 characters.');
        }

        $this->content['Data']['Buyer_RoleRemark'] = $remark;

        return $this;
    }

    /**
     * 設定賣方客戶編號（選填）。
     *
     * @param string $customerNumber
     * @return self
     */
    public function setSellerCustomerNumber(string $customerNumber): self
    {
        $customerNumber = trim($customerNumber);

        if (strlen($customerNumber) > 20) {
            throw new Exception('Seller_CustomerNumber cannot exceed 20 characters.');
        }

        $this->content['Data']['Seller_CustomerNumber'] = $customerNumber;

        return $this;
    }

    /**
     * 設定賣方角色註記（選填）。
     *
     * @param string $remark
     * @return self
     */
    public function setSellerRoleRemark(string $remark): self
    {
        $remark = trim($remark);

        if (mb_strlen($remark) > 40) {
            throw new Exception('Seller_RoleRemark cannot exceed 40 characters.');
        }

        $this->content['Data']['Seller_RoleRemark'] = $remark;

        return $this;
    }

    /**
     * 設定課稅類別（必填）。
     *
     * @param string $taxType 1: 應稅, 2: 零稅率, 3: 免稅, 4: 特種應稅
     * @return self
     */
    public function setTaxType(string $taxType): self
    {
        if (!TaxType::isValid($taxType)) {
            throw new Exception('TaxType must be 1, 2, 3, or 4.');
        }

        $this->content['Data']['TaxType'] = $taxType;

        return $this;
    }

    /**
     * 設定為應稅。
     *
     * @return self
     */
    public function taxable(): self
    {
        return $this->setTaxType(TaxType::TAXABLE);
    }

    /**
     * 設定為零稅率。
     *
     * @param string $reason 零稅率原因：1 非經海關出口、2 經海關出口
     * @return self
     */
    public function zeroTax(string $reason): self
    {
        if (!ZeroTaxRate::isValid($reason)) {
            throw new Exception('ZeroTaxRateReason must be 1 (非經海關出口) or 2 (經海關出口).');
        }

        $this->setTaxType(TaxType::ZERO_TAX);
        $this->content['Data']['ZeroTaxRateReason'] = $reason;

        return $this;
    }

    /**
     * 設定為免稅。
     *
     * @return self
     */
    public function taxFree(): self
    {
        return $this->setTaxType(TaxType::TAX_FREE);
    }

    /**
     * 設定為特種應稅。
     *
     * @param string $specialType 特種稅額類別
     * @return self
     */
    public function specialTax(string $specialType): self
    {
        if (!SpecialTaxType::isValid($specialType)) {
            throw new Exception('SpecialTaxType is invalid.');
        }

        $this->setTaxType(TaxType::SPECIAL_TAX);
        $this->content['Data']['SpecialTaxType'] = $specialType;

        return $this;
    }

    /**
     * 設定零稅率原因（選填）。
     *
     * 當課稅類別為零稅率時，此欄位必填。
     *
     * @param string $reason 1: 非經海關出口, 2: 經海關出口
     * @return self
     */
    public function setZeroTaxRateReason(string $reason): self
    {
        if (!ZeroTaxRate::isValid($reason)) {
            throw new Exception('ZeroTaxRateReason must be 1 or 2.');
        }

        $this->content['Data']['ZeroTaxRateReason'] = $reason;

        return $this;
    }

    /**
     * 設定特種稅額類別（選填）。
     *
     * 當課稅類別為特種應稅時，此欄位必填。
     *
     * @param string $type
     * @return self
     */
    public function setSpecialTaxType(string $type): self
    {
        if (!SpecialTaxType::isValid($type)) {
            throw new Exception('SpecialTaxType is invalid.');
        }

        $this->content['Data']['SpecialTaxType'] = $type;

        return $this;
    }

    /**
     * 設定稅率（選填）。
     *
     * 一般應稅：系統自動設為 0.05。
     * 零稅率/免稅：系統自動設為 0。
     * 特種應稅：依 SpecialTaxType 設定。
     *
     * @param float $rate
     * @return self
     */
    public function setTaxRate(float $rate): self
    {
        $this->content['Data']['TaxRate'] = $rate;

        return $this;
    }

    /**
     * 設定銷售額（必填）。
     *
     * @param int $amount
     * @return self
     */
    public function setSalesAmount(int $amount): self
    {
        if ($amount < 0) {
            throw new Exception('SalesAmount cannot be negative.');
        }

        $this->content['Data']['SalesAmount'] = $amount;

        return $this;
    }

    /**
     * 設定稅額（必填）。
     *
     * @param int $amount
     * @return self
     */
    public function setTaxAmount(int $amount): self
    {
        if ($amount < 0) {
            throw new Exception('TaxAmount cannot be negative.');
        }

        $this->content['Data']['TaxAmount'] = $amount;

        return $this;
    }

    /**
     * 設定總金額（必填）。
     *
     * 銷售額 + 稅額 = 總金額。
     *
     * @param int $amount
     * @return self
     */
    public function setTotalAmount(int $amount): self
    {
        if ($amount < 0) {
            throw new Exception('TotalAmount cannot be negative.');
        }

        $this->content['Data']['TotalAmount'] = $amount;

        return $this;
    }

    /**
     * 設定發票金額（快捷方法）。
     *
     * @param int $salesAmount 銷售額
     * @param int $taxAmount 稅額
     * @param int $totalAmount 總金額
     * @return self
     */
    public function setAmounts(int $salesAmount, int $taxAmount, int $totalAmount): self
    {
        $this->setSalesAmount($salesAmount);
        $this->setTaxAmount($taxAmount);
        $this->setTotalAmount($totalAmount);

        return $this;
    }

    /**
     * 設定發票備註（選填）。
     *
     * @param string $remark
     * @return self
     */
    public function setMainRemark(string $remark): self
    {
        $remark = trim($remark);

        if (mb_strlen($remark) > 200) {
            throw new Exception('MainRemark cannot exceed 200 characters.');
        }

        $this->content['Data']['MainRemark'] = $remark;

        return $this;
    }

    /**
     * 新增商品項目。
     *
     * @param InvoiceItemDto $item
     * @return self
     */
    public function addItem(InvoiceItemDto $item): self
    {
        $this->items->add($item);

        return $this;
    }

    /**
     * 設定商品項目集合。
     *
     * @param ItemCollection $items
     * @return self
     */
    public function setItems(ItemCollection $items): self
    {
        $this->items = $items;

        return $this;
    }

    /**
     * 由陣列新增多個商品項目。
     *
     * @param array<int,array<string,mixed>|InvoiceItemDto> $items
     * @return self
     */
    public function addItemsFromArray(array $items): self
    {
        $this->items = ItemCollection::fromMixed(
            $items,
            fn (array $item): InvoiceItemDto => InvoiceItemDto::fromArray($item)
        );

        return $this;
    }

    /**
     * 取得 payload。
     *
     * @return array
     */
    public function getPayload(): array
    {
        $this->content['Data']['Items'] = $this->items->toArray();

        return parent::getPayload();
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

        // 驗證必填欄位
        if (empty($this->content['Data']['RelateNumber'])) {
            throw new Exception('RelateNumber cannot be empty.');
        }

        if (empty($this->content['Data']['InvoiceDate'])) {
            throw new Exception('InvoiceDate cannot be empty.');
        }

        if (empty($this->content['Data']['Buyer_Identifier'])) {
            throw new Exception('Buyer_Identifier cannot be empty.');
        }

        if (empty($this->content['Data']['Buyer_Name'])) {
            throw new Exception('Buyer_Name cannot be empty.');
        }

        // 驗證字軌類別與課稅類別的搭配
        $invType = $this->content['Data']['InvType'];
        $taxType = $this->content['Data']['TaxType'];

        if ($invType === InvType::GENERAL && !TaxType::isValidForGeneralInvoice($taxType)) {
            throw new Exception('For InvType 07, TaxType must be 1, 2, or 3.');
        }

        if ($invType === InvType::SPECIAL && !TaxType::isValidForSpecialInvoice($taxType)) {
            throw new Exception('For InvType 08, TaxType must be 3 or 4.');
        }

        // 零稅率必須有原因
        if ($taxType === TaxType::ZERO_TAX && empty($this->content['Data']['ZeroTaxRateReason'])) {
            throw new Exception('ZeroTaxRateReason is required when TaxType is 2 (零稅率).');
        }

        // 特種應稅必須有類別
        if ($taxType === TaxType::SPECIAL_TAX && empty($this->content['Data']['SpecialTaxType'])) {
            throw new Exception('SpecialTaxType is required when TaxType is 4 (特種應稅).');
        }

        // 驗證金額
        if ($this->content['Data']['TotalAmount'] <= 0) {
            throw new Exception('TotalAmount must be greater than 0.');
        }

        // 驗證商品
        if ($this->items->isEmpty()) {
            throw new Exception('At least one item is required.');
        }
    }
}
