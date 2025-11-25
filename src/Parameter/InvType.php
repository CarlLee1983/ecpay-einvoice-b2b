<?php

declare(strict_types=1);

namespace ecPay\eInvoiceB2B\Parameter;

/**
 * 字軌類別常數。
 *
 * @see https://developers.ecpay.com.tw/?p=14845
 */
final class InvType
{
    /**
     * 一般稅額發票。
     */
    public const GENERAL = '07';

    /**
     * 特種稅額發票。
     */
    public const SPECIAL = '08';

    /**
     * 有效類別值。
     */
    public const VALID_TYPES = [
        self::GENERAL,
        self::SPECIAL,
    ];
}
