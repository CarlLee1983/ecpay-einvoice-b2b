<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Parameter;

/**
 * 交易對象類型。
 *
 * @see https://developers.ecpay.com.tw/?p=14830
 */
enum CustomerType: string
{
    /**
     * 買方。
     */
    case Buyer = '1';

    /**
     * 賣方。
     */
    case Seller = '2';

    /**
     * 買賣方。
     */
    case Both = '3';

    // ===== 向後相容常數（標記 @deprecated）=====

    /** @deprecated 請改用 CustomerType::Buyer */
    public const BUYER = '1';

    /** @deprecated 請改用 CustomerType::Seller */
    public const SELLER = '2';

    /** @deprecated 請改用 CustomerType::Both */
    public const BOTH = '3';

    /** @deprecated 請改用 CustomerType::cases() */
    public const VALID_TYPES = [
        self::BUYER,
        self::SELLER,
        self::BOTH,
    ];

    /** @deprecated 請改用 $type->label() */
    public const TYPE_NAMES = [
        self::BUYER => '買方',
        self::SELLER => '賣方',
        self::BOTH => '買賣方',
    ];

    // ===== 方法 =====

    /**
     * 取得顯示名稱。
     */
    public function label(): string
    {
        return match ($this) {
            self::Buyer => '買方',
            self::Seller => '賣方',
            self::Both => '買賣方',
        };
    }

    /**
     * 檢查是否為有效的類型值。
     */
    public static function isValid(string $type): bool
    {
        return self::tryFrom($type) !== null;
    }

    /**
     * 取得類型名稱。
     *
     * @deprecated 請改用 CustomerType::tryFrom($type)?->label()
     */
    public static function getName(string $type): ?string
    {
        return self::tryFrom($type)?->label();
    }
}
