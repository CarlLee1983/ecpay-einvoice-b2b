<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Parameter;

/**
 * 發送對象。
 *
 * 用於發送發票通知 API 中的 Notified 參數。
 *
 * @see https://developers.ecpay.com.tw/?p=14988
 */
enum NotifyTarget: string
{
    /**
     * 發送通知給客戶。
     */
    case Customer = 'C';

    /**
     * 發送通知給合作特店。
     */
    case Merchant = 'M';

    /**
     * 皆發送通知。
     */
    case All = 'A';

    // ===== 向後相容常數（標記 @deprecated）=====

    /** @deprecated 請改用 NotifyTarget::Customer */
    public const string CUSTOMER = 'C';

    /** @deprecated 請改用 NotifyTarget::Merchant */
    public const string MERCHANT = 'M';

    /** @deprecated 請改用 NotifyTarget::All */
    public const string ALL = 'A';

    /** @deprecated 請改用 NotifyTarget::cases() */
    public const VALID_TARGETS = [
        self::CUSTOMER,
        self::MERCHANT,
        self::ALL,
    ];

    /** @deprecated 請改用 $target->label() */
    public const TARGET_NAMES = [
        self::CUSTOMER => '客戶',
        self::MERCHANT => '合作特店',
        self::ALL => '皆發送',
    ];

    // ===== 方法 =====

    /**
     * 取得顯示名稱。
     */
    public function label(): string
    {
        return match ($this) {
            self::Customer => '客戶',
            self::Merchant => '合作特店',
            self::All => '皆發送',
        };
    }

    /**
     * 檢查是否為有效的發送對象。
     */
    public static function isValid(string $target): bool
    {
        return self::tryFrom($target) !== null;
    }

    /**
     * 取得對象名稱。
     *
     * @deprecated 請改用 NotifyTarget::tryFrom($target)?->label()
     */
    public static function getName(string $target): ?string
    {
        return self::tryFrom($target)?->label();
    }
}
