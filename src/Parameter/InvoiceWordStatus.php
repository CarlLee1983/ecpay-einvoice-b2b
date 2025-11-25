<?php

declare(strict_types=1);

namespace ecPay\eInvoiceB2B\Parameter;

/**
 * 發票字軌狀態常數（用於設定字軌狀態 API）。
 *
 * @see https://developers.ecpay.com.tw/?p=14840
 */
final class InvoiceWordStatus
{
    /**
     * 停用。
     *
     * 如狀態設定為停用，該字軌區間無法上傳發票。
     */
    public const DISABLED = 0;

    /**
     * 暫停。
     */
    public const SUSPENDED = 1;

    /**
     * 啟用。
     */
    public const ENABLED = 2;

    /**
     * 有效狀態值。
     */
    public const VALID_STATUSES = [
        self::DISABLED,
        self::SUSPENDED,
        self::ENABLED,
    ];

    /**
     * 狀態名稱對應。
     */
    public const STATUS_NAMES = [
        self::DISABLED => '停用',
        self::SUSPENDED => '暫停',
        self::ENABLED => '啟用',
    ];

    /**
     * 檢查是否為有效的狀態值。
     *
     * @param int $status
     * @return bool
     */
    public static function isValid(int $status): bool
    {
        return in_array($status, self::VALID_STATUSES, true);
    }

    /**
     * 取得狀態名稱。
     *
     * @param int $status
     * @return string|null
     */
    public static function getName(int $status): ?string
    {
        return self::STATUS_NAMES[$status] ?? null;
    }
}

