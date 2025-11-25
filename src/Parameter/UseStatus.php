<?php

declare(strict_types=1);

namespace ecPay\eInvoiceB2B\Parameter;

/**
 * 字軌使用狀態常數。
 *
 * @see https://developers.ecpay.com.tw/?p=14845
 */
final class UseStatus
{
    /**
     * 全部狀態。
     */
    public const ALL = 0;

    /**
     * 未啟用。
     */
    public const NOT_ACTIVATED = 1;

    /**
     * 使用中。
     */
    public const IN_USE = 2;

    /**
     * 已停用。
     */
    public const DISABLED = 3;

    /**
     * 暫停中。
     */
    public const SUSPENDED = 4;

    /**
     * 待審核。
     */
    public const PENDING_REVIEW = 5;

    /**
     * 審核不通過。
     */
    public const REVIEW_REJECTED = 6;

    /**
     * 有效狀態值。
     */
    public const VALID_STATUSES = [
        self::ALL,
        self::NOT_ACTIVATED,
        self::IN_USE,
        self::DISABLED,
        self::SUSPENDED,
        self::PENDING_REVIEW,
        self::REVIEW_REJECTED,
    ];
}
