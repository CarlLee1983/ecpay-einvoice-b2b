<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\DTO;

use InvalidArgumentException;

/**
 * 發票商品項目 DTO。
 *
 * 用於開立發票時的商品明細資料。
 *
 * @see https://developers.ecpay.com.tw/?p=14850
 */
final class InvoiceItemDto implements ItemDtoInterface
{
    /**
     * 商品名稱（必填）。
     *
     * @var string
     */
    private string $itemName;

    /**
     * 商品數量（必填）。
     *
     * 支援整數最多12位，小數7位。
     *
     * @var float
     */
    private float $itemCount;

    /**
     * 商品單位（必填）。
     *
     * 例如：個、件、箱、式 等。
     *
     * @var string
     */
    private string $itemWord;

    /**
     * 商品單價（必填）。
     *
     * 支援整數最多12位，小數7位。
     *
     * @var float
     */
    private float $itemPrice;

    /**
     * 商品合計（必填）。
     *
     * 商品數量 * 商品價格 = 商品合計。
     *
     * @var int
     */
    private int $itemAmount;

    /**
     * 商品稅額（選填）。
     *
     * 商品合計 * 稅率 = 商品稅額（四捨五入）。
     * 未帶時會由綠界代為計算。
     * 特種稅額發票請直接帶 0。
     *
     * @var int|null
     */
    private ?int $itemTax;

    /**
     * @param string $itemName 商品名稱
     * @param float $itemCount 商品數量
     * @param string $itemWord 商品單位
     * @param float $itemPrice 商品單價
     * @param int $itemAmount 商品合計
     * @param int|null $itemTax 商品稅額
     */
    public function __construct(
        string $itemName,
        float $itemCount,
        string $itemWord,
        float $itemPrice,
        int $itemAmount,
        ?int $itemTax = null
    ) {
        $this->setItemName($itemName);
        $this->setItemCount($itemCount);
        $this->setItemWord($itemWord);
        $this->setItemPrice($itemPrice);
        $this->setItemAmount($itemAmount);
        $this->itemTax = $itemTax;
    }

    /**
     * 由陣列建立物件。
     *
     * @param array $item
     * @return self
     */
    public static function fromArray(array $item): self
    {
        return new self(
            $item['ItemName'] ?? '',
            (float) ($item['ItemCount'] ?? 0),
            $item['ItemWord'] ?? '',
            (float) ($item['ItemPrice'] ?? 0),
            (int) ($item['ItemAmount'] ?? 0),
            isset($item['ItemTax']) ? (int) $item['ItemTax'] : null
        );
    }

    /**
     * 轉為 API 需要的欄位結構。
     *
     * @return array<string,mixed>
     */
    public function toPayload(): array
    {
        $payload = [
            'ItemName' => $this->itemName,
            'ItemCount' => $this->itemCount,
            'ItemWord' => $this->itemWord,
            'ItemPrice' => $this->itemPrice,
            'ItemAmount' => $this->itemAmount,
        ];

        if ($this->itemTax !== null) {
            $payload['ItemTax'] = $this->itemTax;
        }

        return $payload;
    }

    /**
     * 取得金額（單價 * 數量）。
     *
     * @return float
     */
    public function getAmount(): float
    {
        return (float) $this->itemAmount;
    }

    /**
     * 設定商品名稱。
     *
     * @param string $itemName
     * @return void
     */
    private function setItemName(string $itemName): void
    {
        $itemName = trim($itemName);

        if ($itemName === '') {
            throw new InvalidArgumentException('ItemName cannot be empty.');
        }

        if (mb_strlen($itemName) > 256) {
            throw new InvalidArgumentException('ItemName cannot exceed 256 characters.');
        }

        $this->itemName = $itemName;
    }

    /**
     * 設定商品數量。
     *
     * @param float $itemCount
     * @return void
     */
    private function setItemCount(float $itemCount): void
    {
        if ($itemCount <= 0) {
            throw new InvalidArgumentException('ItemCount must be greater than 0.');
        }

        $this->itemCount = $itemCount;
    }

    /**
     * 設定商品單位。
     *
     * @param string $itemWord
     * @return void
     */
    private function setItemWord(string $itemWord): void
    {
        $itemWord = trim($itemWord);

        if ($itemWord === '') {
            throw new InvalidArgumentException('ItemWord cannot be empty.');
        }

        if (mb_strlen($itemWord) > 6) {
            throw new InvalidArgumentException('ItemWord cannot exceed 6 characters.');
        }

        $this->itemWord = $itemWord;
    }

    /**
     * 設定商品單價。
     *
     * @param float $itemPrice
     * @return void
     */
    private function setItemPrice(float $itemPrice): void
    {
        $this->itemPrice = $itemPrice;
    }

    /**
     * 設定商品合計。
     *
     * @param int $itemAmount
     * @return void
     */
    private function setItemAmount(int $itemAmount): void
    {
        $this->itemAmount = $itemAmount;
    }

    /**
     * 取得商品名稱。
     *
     * @return string
     */
    public function getItemName(): string
    {
        return $this->itemName;
    }

    /**
     * 取得商品數量。
     *
     * @return float
     */
    public function getItemCount(): float
    {
        return $this->itemCount;
    }

    /**
     * 取得商品單位。
     *
     * @return string
     */
    public function getItemWord(): string
    {
        return $this->itemWord;
    }

    /**
     * 取得商品單價。
     *
     * @return float
     */
    public function getItemPrice(): float
    {
        return $this->itemPrice;
    }

    /**
     * 取得商品合計。
     *
     * @return int
     */
    public function getItemAmount(): int
    {
        return $this->itemAmount;
    }

    /**
     * 取得商品稅額。
     *
     * @return int|null
     */
    public function getItemTax(): ?int
    {
        return $this->itemTax;
    }
}
