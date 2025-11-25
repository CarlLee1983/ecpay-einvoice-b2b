<?php

declare(strict_types=1);

namespace ecPay\eInvoiceB2B\Parameter;

/**
 * 發票期別常數。
 *
 * @see https://developers.ecpay.com.tw/?p=14845
 */
final class InvoiceTerm
{
    /**
     * 全部期別。
     */
    public const ALL = 0;

    /**
     * 1-2 月。
     */
    public const JAN_FEB = 1;

    /**
     * 3-4 月。
     */
    public const MAR_APR = 2;

    /**
     * 5-6 月。
     */
    public const MAY_JUN = 3;

    /**
     * 7-8 月。
     */
    public const JUL_AUG = 4;

    /**
     * 9-10 月。
     */
    public const SEP_OCT = 5;

    /**
     * 11-12 月。
     */
    public const NOV_DEC = 6;

    /**
     * 有效期別值。
     */
    public const VALID_TERMS = [
        self::ALL,
        self::JAN_FEB,
        self::MAR_APR,
        self::MAY_JUN,
        self::JUL_AUG,
        self::SEP_OCT,
        self::NOV_DEC,
    ];
}
