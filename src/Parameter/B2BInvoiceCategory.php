<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Parameter;

/**
 * B2B 發票種類（用於查詢發票）。
 *
 * @see https://developers.ecpay.com.tw/?p=14935
 */
enum B2BInvoiceCategory: int
{
    /**
     * 銷項發票。
     *
     * 查詢特店開給交易相對人的發票明細。
     */
    case Sales = 0;

    /**
     * 進項發票。
     *
     * 查詢交易相對人開給特店的發票明細。
     */
    case Purchase = 1;

    // ===== 向後相容常數（標記 @deprecated）=====

    /** @deprecated 請改用 B2BInvoiceCategory::Sales */
    public const int SALES = 0;

    /** @deprecated 請改用 B2BInvoiceCategory::Purchase */
    public const int PURCHASE = 1;

    /** @deprecated 請改用 B2BInvoiceCategory::cases() */
    public const VALID_CATEGORIES = [
        self::SALES,
        self::PURCHASE,
    ];

    /** @deprecated 請改用 $category->label() */
    public const CATEGORY_NAMES = [
        self::SALES => '銷項發票',
        self::PURCHASE => '進項發票',
    ];

    // ===== 方法 =====

    /**
     * 取得顯示名稱。
     */
    public function label(): string
    {
        return match ($this) {
            self::Sales => '銷項發票',
            self::Purchase => '進項發票',
        };
    }

    /**
     * 檢查是否為有效的種類值。
     */
    public static function isValid(int $category): bool
    {
        return self::tryFrom($category) !== null;
    }

    /**
     * 取得種類名稱。
     *
     * @deprecated 請改用 B2BInvoiceCategory::tryFrom($category)?->label()
     */
    public static function getName(int $category): ?string
    {
        return self::tryFrom($category)?->label();
    }
}
