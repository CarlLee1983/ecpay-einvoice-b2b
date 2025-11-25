<?php

declare(strict_types=1);

namespace ecPay\eInvoiceB2B\Parameter;

/**
 * B2B 發票種類常數（用於查詢發票）。
 *
 * @see https://developers.ecpay.com.tw/?p=14935
 */
final class B2BInvoiceCategory
{
    /**
     * 銷項發票。
     *
     * 查詢特店開給交易相對人的發票明細。
     */
    public const SALES = 0;

    /**
     * 進項發票。
     *
     * 查詢交易相對人開給特店的發票明細。
     */
    public const PURCHASE = 1;

    /**
     * 有效種類值。
     */
    public const VALID_CATEGORIES = [
        self::SALES,
        self::PURCHASE,
    ];

    /**
     * 種類名稱對應。
     */
    public const CATEGORY_NAMES = [
        self::SALES => '銷項發票',
        self::PURCHASE => '進項發票',
    ];

    /**
     * 檢查是否為有效的種類值。
     *
     * @param int $category
     * @return bool
     */
    public static function isValid(int $category): bool
    {
        return in_array($category, self::VALID_CATEGORIES, true);
    }

    /**
     * 取得種類名稱。
     *
     * @param int $category
     * @return string|null
     */
    public static function getName(int $category): ?string
    {
        return self::CATEGORY_NAMES[$category] ?? null;
    }
}

