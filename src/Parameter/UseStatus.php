<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Parameter;

/**
 * 字軌使用狀態。
 *
 * @see https://developers.ecpay.com.tw/?p=14845
 */
enum UseStatus: int
{
    /**
     * 全部狀態。
     */
    case All = 0;

    /**
     * 未啟用。
     */
    case NotActivated = 1;

    /**
     * 使用中。
     */
    case InUse = 2;

    /**
     * 已停用。
     */
    case Disabled = 3;

    /**
     * 暫停中。
     */
    case Suspended = 4;

    /**
     * 待審核。
     */
    case PendingReview = 5;

    /**
     * 審核不通過。
     */
    case ReviewRejected = 6;

    // ===== 向後相容常數（標記 @deprecated）=====

    /** @deprecated 請改用 UseStatus::All */
    public const ALL = 0;

    /** @deprecated 請改用 UseStatus::NotActivated */
    public const NOT_ACTIVATED = 1;

    /** @deprecated 請改用 UseStatus::InUse */
    public const IN_USE = 2;

    /** @deprecated 請改用 UseStatus::Disabled */
    public const DISABLED = 3;

    /** @deprecated 請改用 UseStatus::Suspended */
    public const SUSPENDED = 4;

    /** @deprecated 請改用 UseStatus::PendingReview */
    public const PENDING_REVIEW = 5;

    /** @deprecated 請改用 UseStatus::ReviewRejected */
    public const REVIEW_REJECTED = 6;

    /** @deprecated 請改用 UseStatus::cases() */
    public const VALID_STATUSES = [
        self::ALL,
        self::NOT_ACTIVATED,
        self::IN_USE,
        self::DISABLED,
        self::SUSPENDED,
        self::PENDING_REVIEW,
        self::REVIEW_REJECTED,
    ];

    // ===== 方法 =====

    /**
     * 取得顯示名稱。
     */
    public function label(): string
    {
        return match ($this) {
            self::All => '全部狀態',
            self::NotActivated => '未啟用',
            self::InUse => '使用中',
            self::Disabled => '已停用',
            self::Suspended => '暫停中',
            self::PendingReview => '待審核',
            self::ReviewRejected => '審核不通過',
        };
    }

    /**
     * 檢查是否為有效的狀態值。
     */
    public static function isValid(int $value): bool
    {
        return self::tryFrom($value) !== null;
    }
}
