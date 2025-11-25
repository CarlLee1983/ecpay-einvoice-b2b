<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Parameter;

/**
 * 發送對象常數。
 *
 * 用於發送發票通知 API 中的 Notified 參數。
 *
 * @see https://developers.ecpay.com.tw/?p=14988
 */
final class NotifyTarget
{
    /**
     * 發送通知給客戶。
     */
    public const CUSTOMER = 'C';

    /**
     * 發送通知給合作特店。
     */
    public const MERCHANT = 'M';

    /**
     * 皆發送通知。
     */
    public const ALL = 'A';

    /**
     * 有效對象值。
     */
    public const VALID_TARGETS = [
        self::CUSTOMER,
        self::MERCHANT,
        self::ALL,
    ];

    /**
     * 對象名稱對應。
     */
    public const TARGET_NAMES = [
        self::CUSTOMER => '客戶',
        self::MERCHANT => '合作特店',
        self::ALL => '皆發送',
    ];

    /**
     * 檢查是否為有效的發送對象。
     *
     * @param string $target
     * @return bool
     */
    public static function isValid(string $target): bool
    {
        return in_array($target, self::VALID_TARGETS, true);
    }

    /**
     * 取得對象名稱。
     *
     * @param string $target
     * @return string|null
     */
    public static function getName(string $target): ?string
    {
        return self::TARGET_NAMES[$target] ?? null;
    }
}

