<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Parameter;

/**
 * 發票字軌狀態（用於設定字軌狀態 API）。
 *
 * @see https://developers.ecpay.com.tw/?p=14840
 */
enum InvoiceWordStatus: int
{
    /**
     * 停用。
     *
     * 如狀態設定為停用，該字軌區間無法上傳發票。
     */
    case Disabled = 0;

    /**
     * 暫停。
     */
    case Suspended = 1;

    /**
     * 啟用。
     */
    case Enabled = 2;

    // ===== 向後相容常數（標記 @deprecated）=====

    /** @deprecated 請改用 InvoiceWordStatus::Disabled */
    public const int DISABLED = 0;

    /** @deprecated 請改用 InvoiceWordStatus::Suspended */
    public const int SUSPENDED = 1;

    /** @deprecated 請改用 InvoiceWordStatus::Enabled */
    public const int ENABLED = 2;

    /** @deprecated 請改用 InvoiceWordStatus::cases() */
    public const VALID_STATUSES = [
        self::DISABLED,
        self::SUSPENDED,
        self::ENABLED,
    ];

    /** @deprecated 請改用 $status->label() */
    public const STATUS_NAMES = [
        self::DISABLED => '停用',
        self::SUSPENDED => '暫停',
        self::ENABLED => '啟用',
    ];

    // ===== 方法 =====

    /**
     * 取得顯示名稱。
     */
    public function label(): string
    {
        return match ($this) {
            self::Disabled => '停用',
            self::Suspended => '暫停',
            self::Enabled => '啟用',
        };
    }

    /**
     * 檢查是否為有效的狀態值。
     */
    public static function isValid(int $status): bool
    {
        return self::tryFrom($status) !== null;
    }

    /**
     * 取得狀態名稱。
     *
     * @deprecated 請改用 InvoiceWordStatus::tryFrom($status)?->label()
     */
    public static function getName(int $status): ?string
    {
        return self::tryFrom($status)?->label();
    }
}
