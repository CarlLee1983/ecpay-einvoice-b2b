<?php

declare(strict_types=1);

namespace ecPay\eInvoiceB2B\Parameter;

/**
 * 發送內容類型常數。
 *
 * 用於發送發票通知 API 中的 InvoiceTag 參數。
 *
 * @see https://developers.ecpay.com.tw/?p=14988
 */
final class InvoiceTag
{
    /**
     * 發票開立。
     */
    public const ISSUE = '1';

    /**
     * 發票作廢。
     */
    public const INVALID = '2';

    /**
     * 發票退回。
     */
    public const REJECT = '3';

    /**
     * 開立折讓。
     */
    public const ALLOWANCE = '4';

    /**
     * 作廢折讓。
     */
    public const ALLOWANCE_INVALID = '5';

    /**
     * 開立發票確認。
     */
    public const ISSUE_CONFIRM = '6';

    /**
     * 作廢發票確認。
     */
    public const INVALID_CONFIRM = '7';

    /**
     * 退回發票確認。
     */
    public const REJECT_CONFIRM = '8';

    /**
     * 折讓確認。
     */
    public const ALLOWANCE_CONFIRM = '9';

    /**
     * 作廢折讓確認。
     */
    public const ALLOWANCE_INVALID_CONFIRM = '10';

    /**
     * 有效標籤值。
     */
    public const VALID_TAGS = [
        self::ISSUE,
        self::INVALID,
        self::REJECT,
        self::ALLOWANCE,
        self::ALLOWANCE_INVALID,
        self::ISSUE_CONFIRM,
        self::INVALID_CONFIRM,
        self::REJECT_CONFIRM,
        self::ALLOWANCE_CONFIRM,
        self::ALLOWANCE_INVALID_CONFIRM,
    ];

    /**
     * 標籤名稱對應。
     */
    public const TAG_NAMES = [
        self::ISSUE => '發票開立',
        self::INVALID => '發票作廢',
        self::REJECT => '發票退回',
        self::ALLOWANCE => '開立折讓',
        self::ALLOWANCE_INVALID => '作廢折讓',
        self::ISSUE_CONFIRM => '開立發票確認',
        self::INVALID_CONFIRM => '作廢發票確認',
        self::REJECT_CONFIRM => '退回發票確認',
        self::ALLOWANCE_CONFIRM => '折讓確認',
        self::ALLOWANCE_INVALID_CONFIRM => '作廢折讓確認',
    ];

    /**
     * 需要折讓單編號的標籤。
     */
    public const ALLOWANCE_TAGS = [
        self::ALLOWANCE,
        self::ALLOWANCE_INVALID,
        self::ALLOWANCE_CONFIRM,
        self::ALLOWANCE_INVALID_CONFIRM,
    ];

    /**
     * 檢查是否為有效的標籤。
     *
     * @param string $tag
     * @return bool
     */
    public static function isValid(string $tag): bool
    {
        return in_array($tag, self::VALID_TAGS, true);
    }

    /**
     * 取得標籤名稱。
     *
     * @param string $tag
     * @return string|null
     */
    public static function getName(string $tag): ?string
    {
        return self::TAG_NAMES[$tag] ?? null;
    }

    /**
     * 檢查是否為折讓相關標籤。
     *
     * @param string $tag
     * @return bool
     */
    public static function isAllowanceTag(string $tag): bool
    {
        return in_array($tag, self::ALLOWANCE_TAGS, true);
    }
}

