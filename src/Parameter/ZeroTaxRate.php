<?php

declare(strict_types=1);

namespace ecPay\eInvoiceB2B\Parameter;

/**
 * 零稅率類型常數。
 *
 * 當課稅類別 TaxType 為 2（零稅率）時，此參數必填。
 *
 * @see https://developers.ecpay.com.tw/?p=14850
 */
final class ZeroTaxRate
{
    /**
     * 非經海關出口。
     */
    public const NON_CUSTOMS_EXPORT = '1';

    /**
     * 經海關出口。
     */
    public const CUSTOMS_EXPORT = '2';

    /**
     * 有效類別值。
     */
    public const VALID_TYPES = [
        self::NON_CUSTOMS_EXPORT,
        self::CUSTOMS_EXPORT,
    ];

    /**
     * 類別名稱對應。
     */
    public const TYPE_NAMES = [
        self::NON_CUSTOMS_EXPORT => '非經海關出口',
        self::CUSTOMS_EXPORT => '經海關出口',
    ];

    /**
     * 檢查是否為有效的零稅率類型。
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

