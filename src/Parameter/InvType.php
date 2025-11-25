<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Parameter;

/**
 * 字軌類別。
 *
 * @see https://developers.ecpay.com.tw/?p=14845
 */
enum InvType: string
{
    /**
     * 一般稅額發票。
     */
    case General = '07';

    /**
     * 特種稅額發票。
     */
    case Special = '08';

    // ===== 向後相容常數（標記 @deprecated）=====

    /** @deprecated 請改用 InvType::General */
    public const string GENERAL = '07';

    /** @deprecated 請改用 InvType::Special */
    public const string SPECIAL = '08';

    /** @deprecated 請改用 InvType::cases() */
    public const VALID_TYPES = [
        self::GENERAL,
        self::SPECIAL,
    ];

    // ===== 方法 =====

    /**
     * 取得顯示名稱。
     */
    public function label(): string
    {
        return match ($this) {
            self::General => '一般稅額發票',
            self::Special => '特種稅額發票',
        };
    }

    /**
     * 檢查是否為有效的字軌類別。
     */
    public static function isValid(string $value): bool
    {
        return self::tryFrom($value) !== null;
    }
}
