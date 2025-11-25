<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Parameter;

/**
 * 發票類別。
 *
 * @see https://developers.ecpay.com.tw/?p=14845
 */
enum InvoiceCategory: int
{
    /**
     * B2B 發票類別。
     */
    case B2B = 2;

    // ===== 向後相容常數（標記 @deprecated）=====

    /** @deprecated 請改用 InvoiceCategory::B2B->value */
    public const B2B_VALUE = 2;

    // ===== 方法 =====

    /**
     * 取得顯示名稱。
     */
    public function label(): string
    {
        return match ($this) {
            self::B2B => 'B2B 發票',
        };
    }

    /**
     * 檢查是否為有效的發票類別。
     */
    public static function isValid(int $value): bool
    {
        return self::tryFrom($value) !== null;
    }
}
