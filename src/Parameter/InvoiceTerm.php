<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Parameter;

/**
 * 發票期別。
 *
 * @see https://developers.ecpay.com.tw/?p=14845
 */
enum InvoiceTerm: int
{
    /**
     * 全部期別。
     */
    case All = 0;

    /**
     * 1-2 月。
     */
    case JanFeb = 1;

    /**
     * 3-4 月。
     */
    case MarApr = 2;

    /**
     * 5-6 月。
     */
    case MayJun = 3;

    /**
     * 7-8 月。
     */
    case JulAug = 4;

    /**
     * 9-10 月。
     */
    case SepOct = 5;

    /**
     * 11-12 月。
     */
    case NovDec = 6;

    // ===== 向後相容常數（標記 @deprecated）=====

    /** @deprecated 請改用 InvoiceTerm::All */
    public const ALL = 0;

    /** @deprecated 請改用 InvoiceTerm::JanFeb */
    public const JAN_FEB = 1;

    /** @deprecated 請改用 InvoiceTerm::MarApr */
    public const MAR_APR = 2;

    /** @deprecated 請改用 InvoiceTerm::MayJun */
    public const MAY_JUN = 3;

    /** @deprecated 請改用 InvoiceTerm::JulAug */
    public const JUL_AUG = 4;

    /** @deprecated 請改用 InvoiceTerm::SepOct */
    public const SEP_OCT = 5;

    /** @deprecated 請改用 InvoiceTerm::NovDec */
    public const NOV_DEC = 6;

    /** @deprecated 請改用 InvoiceTerm::cases() */
    public const VALID_TERMS = [
        self::ALL,
        self::JAN_FEB,
        self::MAR_APR,
        self::MAY_JUN,
        self::JUL_AUG,
        self::SEP_OCT,
        self::NOV_DEC,
    ];

    // ===== 方法 =====

    /**
     * 取得顯示名稱。
     */
    public function label(): string
    {
        return match ($this) {
            self::All => '全部期別',
            self::JanFeb => '1-2 月',
            self::MarApr => '3-4 月',
            self::MayJun => '5-6 月',
            self::JulAug => '7-8 月',
            self::SepOct => '9-10 月',
            self::NovDec => '11-12 月',
        };
    }

    /**
     * 檢查是否為有效的期別值。
     */
    public static function isValid(int $term): bool
    {
        return self::tryFrom($term) !== null;
    }

    /**
     * 依照月份取得對應的期別。
     */
    public static function fromMonth(int $month): self
    {
        return match (true) {
            $month >= 1 && $month <= 2 => self::JanFeb,
            $month >= 3 && $month <= 4 => self::MarApr,
            $month >= 5 && $month <= 6 => self::MayJun,
            $month >= 7 && $month <= 8 => self::JulAug,
            $month >= 9 && $month <= 10 => self::SepOct,
            $month >= 11 && $month <= 12 => self::NovDec,
            default => self::All,
        };
    }
}
