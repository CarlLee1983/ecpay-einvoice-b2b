<?php

declare(strict_types=1);

namespace ecPay\eInvoiceB2B\Parameter;

/**
 * 課稅類別常數。
 *
 * @see https://developers.ecpay.com.tw/?p=14850
 */
final class TaxType
{
    /**
     * 一般應稅。
     */
    public const TAXABLE = '1';

    /**
     * 零稅率。
     */
    public const ZERO_TAX = '2';

    /**
     * 免稅。
     */
    public const TAX_FREE = '3';

    /**
     * 特種應稅。
     *
     * 僅適用於字軌類別 InvType 為 08（特種稅額計算之電子發票）時使用。
     */
    public const SPECIAL_TAX = '4';

    /**
     * 有效類別值。
     */
    public const VALID_TYPES = [
        self::TAXABLE,
        self::ZERO_TAX,
        self::TAX_FREE,
        self::SPECIAL_TAX,
    ];

    /**
     * 一般稅額發票（InvType=07）可用的類別。
     */
    public const GENERAL_INVOICE_TYPES = [
        self::TAXABLE,
        self::ZERO_TAX,
        self::TAX_FREE,
    ];

    /**
     * 特種稅額發票（InvType=08）可用的類別。
     */
    public const SPECIAL_INVOICE_TYPES = [
        self::TAX_FREE,
        self::SPECIAL_TAX,
    ];

    /**
     * 類別名稱對應。
     */
    public const TYPE_NAMES = [
        self::TAXABLE => '應稅',
        self::ZERO_TAX => '零稅率',
        self::TAX_FREE => '免稅',
        self::SPECIAL_TAX => '特種應稅',
    ];

    /**
     * 檢查是否為有效的課稅類別。
     *
     * @param string $type
     * @return bool
     */
    public static function isValid(string $type): bool
    {
        return in_array($type, self::VALID_TYPES, true);
    }

    /**
     * 檢查是否為一般稅額發票可用的類別。
     *
     * @param string $type
     * @return bool
     */
    public static function isValidForGeneralInvoice(string $type): bool
    {
        return in_array($type, self::GENERAL_INVOICE_TYPES, true);
    }

    /**
     * 檢查是否為特種稅額發票可用的類別。
     *
     * @param string $type
     * @return bool
     */
    public static function isValidForSpecialInvoice(string $type): bool
    {
        return in_array($type, self::SPECIAL_INVOICE_TYPES, true);
    }

    /**
     * 取得類別名稱。
     *
     * @param string $type
     * @return string|null
     */
    public static function getName(string $type): ?string
    {
        return self::TYPE_NAMES[$type] ?? null;
    }
}

