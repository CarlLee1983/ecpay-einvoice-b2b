<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Parameter;

/**
 * 零稅率類型。
 *
 * 當課稅類別 TaxType 為 2（零稅率）時，此參數必填。
 *
 * @see https://developers.ecpay.com.tw/?p=14850
 */
enum ZeroTaxRate: string
{
    /**
     * 非經海關出口。
     */
    case NonCustomsExport = '1';

    /**
     * 經海關出口。
     */
    case CustomsExport = '2';

    // ===== 向後相容常數（標記 @deprecated）=====

    /** @deprecated 請改用 ZeroTaxRate::NonCustomsExport */
    public const string NON_CUSTOMS_EXPORT = '1';

    /** @deprecated 請改用 ZeroTaxRate::CustomsExport */
    public const string CUSTOMS_EXPORT = '2';

    /** @deprecated 請改用 ZeroTaxRate::cases() */
    public const VALID_TYPES = [
        self::NON_CUSTOMS_EXPORT,
        self::CUSTOMS_EXPORT,
    ];

    /** @deprecated 請改用 $zeroTaxRate->label() */
    public const TYPE_NAMES = [
        self::NON_CUSTOMS_EXPORT => '非經海關出口',
        self::CUSTOMS_EXPORT => '經海關出口',
    ];

    // ===== 方法 =====

    /**
     * 取得顯示名稱。
     */
    public function label(): string
    {
        return match ($this) {
            self::NonCustomsExport => '非經海關出口',
            self::CustomsExport => '經海關出口',
        };
    }

    /**
     * 檢查是否為有效的零稅率類型。
     */
    public static function isValid(string $type): bool
    {
        return self::tryFrom($type) !== null;
    }

    /**
     * 取得類型名稱。
     *
     * @deprecated 請改用 ZeroTaxRate::tryFrom($type)?->label()
     */
    public static function getName(string $type): ?string
    {
        return self::tryFrom($type)?->label();
    }
}
