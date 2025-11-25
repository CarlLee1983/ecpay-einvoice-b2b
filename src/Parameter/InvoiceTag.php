<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Parameter;

/**
 * 發送內容類型。
 *
 * 用於發送發票通知 API 中的 InvoiceTag 參數。
 *
 * @see https://developers.ecpay.com.tw/?p=14988
 */
enum InvoiceTag: string
{
    /**
     * 發票開立。
     */
    case Issue = '1';

    /**
     * 發票作廢。
     */
    case Invalid = '2';

    /**
     * 發票退回。
     */
    case Reject = '3';

    /**
     * 開立折讓。
     */
    case Allowance = '4';

    /**
     * 作廢折讓。
     */
    case AllowanceInvalid = '5';

    /**
     * 開立發票確認。
     */
    case IssueConfirm = '6';

    /**
     * 作廢發票確認。
     */
    case InvalidConfirm = '7';

    /**
     * 退回發票確認。
     */
    case RejectConfirm = '8';

    /**
     * 折讓確認。
     */
    case AllowanceConfirm = '9';

    /**
     * 作廢折讓確認。
     */
    case AllowanceInvalidConfirm = '10';

    // ===== 向後相容常數（標記 @deprecated）=====

    /** @deprecated 請改用 InvoiceTag::Issue */
    public const ISSUE = '1';

    /** @deprecated 請改用 InvoiceTag::Invalid */
    public const INVALID = '2';

    /** @deprecated 請改用 InvoiceTag::Reject */
    public const REJECT = '3';

    /** @deprecated 請改用 InvoiceTag::Allowance */
    public const ALLOWANCE = '4';

    /** @deprecated 請改用 InvoiceTag::AllowanceInvalid */
    public const ALLOWANCE_INVALID = '5';

    /** @deprecated 請改用 InvoiceTag::IssueConfirm */
    public const ISSUE_CONFIRM = '6';

    /** @deprecated 請改用 InvoiceTag::InvalidConfirm */
    public const INVALID_CONFIRM = '7';

    /** @deprecated 請改用 InvoiceTag::RejectConfirm */
    public const REJECT_CONFIRM = '8';

    /** @deprecated 請改用 InvoiceTag::AllowanceConfirm */
    public const ALLOWANCE_CONFIRM = '9';

    /** @deprecated 請改用 InvoiceTag::AllowanceInvalidConfirm */
    public const ALLOWANCE_INVALID_CONFIRM = '10';

    /** @deprecated 請改用 InvoiceTag::cases() */
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

    /** @deprecated 請改用 $tag->label() */
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

    /** @deprecated 請改用 InvoiceTag::allowanceTags() */
    public const ALLOWANCE_TAGS = [
        self::ALLOWANCE,
        self::ALLOWANCE_INVALID,
        self::ALLOWANCE_CONFIRM,
        self::ALLOWANCE_INVALID_CONFIRM,
    ];

    // ===== 方法 =====

    /**
     * 取得顯示名稱。
     */
    public function label(): string
    {
        return match ($this) {
            self::Issue => '發票開立',
            self::Invalid => '發票作廢',
            self::Reject => '發票退回',
            self::Allowance => '開立折讓',
            self::AllowanceInvalid => '作廢折讓',
            self::IssueConfirm => '開立發票確認',
            self::InvalidConfirm => '作廢發票確認',
            self::RejectConfirm => '退回發票確認',
            self::AllowanceConfirm => '折讓確認',
            self::AllowanceInvalidConfirm => '作廢折讓確認',
        };
    }

    /**
     * 檢查是否為有效的標籤。
     */
    public static function isValid(string $tag): bool
    {
        return self::tryFrom($tag) !== null;
    }

    /**
     * 取得折讓相關標籤。
     *
     * @return array<InvoiceTag>
     */
    public static function allowanceTags(): array
    {
        return [
            self::Allowance,
            self::AllowanceInvalid,
            self::AllowanceConfirm,
            self::AllowanceInvalidConfirm,
        ];
    }

    /**
     * 檢查此實例是否為折讓相關標籤。
     */
    public function isAllowance(): bool
    {
        return in_array($this, self::allowanceTags(), true);
    }

    /**
     * 檢查是否為折讓相關標籤（靜態方法，向後相容）。
     */
    public static function isAllowanceTag(string $tag): bool
    {
        $enum = self::tryFrom($tag);

        return $enum !== null && $enum->isAllowance();
    }

    /**
     * 取得標籤名稱。
     *
     * @deprecated 請改用 InvoiceTag::tryFrom($tag)?->label()
     */
    public static function getName(string $tag): ?string
    {
        return self::tryFrom($tag)?->label();
    }
}
