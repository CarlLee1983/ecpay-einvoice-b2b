<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Operations;

use CarlLee\EcPayB2B\Content;
use CarlLee\EcPayB2B\DTO\AllowanceItemDto;
use CarlLee\EcPayB2B\DTO\ItemCollection;
use CarlLee\EcPayB2B\Parameter\TaxType;
use Exception;

/**
 * 開立折讓發票 API。
 *
 * 賣方開立折讓單：
 * 根據財政部電子發票 MIG 4.1 版規定，B2B電子發票折讓作業僅能由原發票開立人（即賣方）發動。
 * 當特店（營業人）發票開立後，如發生銷貨退回、調換貨物或折讓等情形，應與買方（交易相對人）達成協議後，
 * 由賣方發起折讓流程，並傳送折讓發票參數至綠界科技，由綠界上傳至財政部電子發票整合服務平台，
 * 再依設定通知買方折讓資訊。
 *
 * @see https://developers.ecpay.com.tw/?p=14923
 */
class Allowance extends Content
{
    /**
     * API endpoint path.
     *
     * @var string
     */
    protected $requestPath = '/B2BInvoice/Allowance';

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
            'AllowanceDate' => '',
            'InvoiceNumber' => '',
            'InvoiceDate' => '',
            'Buyer_Identifier' => '',
            'Buyer_Name' => '',
            'AllowanceAmount' => 0,
            'TaxAmount' => 0,
            'Items' => [],
        ];
    }

    /**
     * 設定自訂編號（必填）。
     *
     * 均為唯一值不可重覆使用。
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
     * 設定原發票號碼（必填）。
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
     * 設定原發票開立日期（必填）。
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
     * 設定折讓金額（必填）。
     *
     * @param int $amount
     * @return self
     */
    public function setAllowanceAmount(int $amount): self
    {
        if ($amount <= 0) {
            throw new Exception('AllowanceAmount must be greater than 0.');
        }

        $this->content['Data']['AllowanceAmount'] = $amount;

        return $this;
    }

    /**
     * 設定折讓稅額（選填）。
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
     * 新增折讓商品項目。
     *
     * @param AllowanceItemDto $item
     * @return self
     */
    public function addItem(AllowanceItemDto $item): self
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
     * @param array<int,array<string,mixed>|AllowanceItemDto> $items
     * @return self
     */
    public function addItemsFromArray(array $items): self
    {
        $this->items = ItemCollection::fromMixed(
            $items,
            fn (array $item): AllowanceItemDto => AllowanceItemDto::fromArray($item)
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

        if (empty($this->content['Data']['RelateNumber'])) {
            throw new Exception('RelateNumber cannot be empty.');
        }

        if (empty($this->content['Data']['AllowanceDate'])) {
            throw new Exception('AllowanceDate cannot be empty.');
        }

        if (empty($this->content['Data']['InvoiceNumber'])) {
            throw new Exception('InvoiceNumber cannot be empty.');
        }

        if (!preg_match('/^[A-Z]{2}\d{8}$/', $this->content['Data']['InvoiceNumber'])) {
            throw new Exception('InvoiceNumber must be 2 letters followed by 8 digits.');
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

        if ($this->content['Data']['AllowanceAmount'] <= 0) {
            throw new Exception('AllowanceAmount must be greater than 0.');
        }

        if ($this->items->isEmpty()) {
            throw new Exception('At least one item is required.');
        }
    }
}
