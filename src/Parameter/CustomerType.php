<?php

declare(strict_types=1);

namespace ecPay\eInvoiceB2B\Parameter;

/**
 * 交易對象類型常數。
 *
 * @see https://developers.ecpay.com.tw/?p=14830
 */
final class CustomerType
{
    /**
     * 買方。
     */
    public const BUYER = '1';

    /**
     * 賣方。
     */
    public const SELLER = '2';

    /**
     * 買賣方。
     */
    public const BOTH = '3';

    /**
     * 有效類型值。
     */
    public const VALID_TYPES = [
        self::BUYER,
        self::SELLER,
        self::BOTH,
    ];

    /**
     * 類型名稱對應。
     */
    public const TYPE_NAMES = [
        self::BUYER => '買方',
        self::SELLER => '賣方',
        self::BOTH => '買賣方',
    ];

    /**
     * 檢查是否為有效的類型值。
     *
     * @param string $type
     * @return bool
     */
    public static function isValid(string $type): bool
    {
        return in_array($type, self::VALID_TYPES, true);
    }

    /**
     * 取得類型名稱。
     *
     * @param string $type
     * @return string|null
     */
    public static function getName(string $type): ?string
    {
        return self::TYPE_NAMES[$type] ?? null;
    }
}

