<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Parameter;

/**
 * 課稅類別。
 *
 * @see https://developers.ecpay.com.tw/?p=14850
 */
enum TaxType: string
{
    /**
     * 一般應稅。
     */
    case Taxable = '1';

    /**
     * 零稅率。
     */
    case ZeroTax = '2';

    /**
     * 免稅。
     */
    case TaxFree = '3';

    /**
     * 特種應稅。
     *
     * 僅適用於字軌類別 InvType 為 08（特種稅額計算之電子發票）時使用。
     */
    case SpecialTax = '4';

    // ===== 向後相容常數（標記 @deprecated）=====

    /** @deprecated 請改用 TaxType::Taxable */
    public const TAXABLE = '1';

    /** @deprecated 請改用 TaxType::ZeroTax */
    public const ZERO_TAX = '2';

    /** @deprecated 請改用 TaxType::TaxFree */
    public const TAX_FREE = '3';

    /** @deprecated 請改用 TaxType::SpecialTax */
    public const SPECIAL_TAX = '4';

    /** @deprecated 請改用 TaxType::cases() */
    public const VALID_TYPES = [
        self::TAXABLE,
        self::ZERO_TAX,
        self::TAX_FREE,
        self::SPECIAL_TAX,
    ];

    /** @deprecated 請改用 TaxType::generalInvoiceTypes() */
    public const GENERAL_INVOICE_TYPES = [
        self::TAXABLE,
        self::ZERO_TAX,
        self::TAX_FREE,
    ];

    /** @deprecated 請改用 TaxType::specialInvoiceTypes() */
    public const SPECIAL_INVOICE_TYPES = [
        self::TAX_FREE,
        self::SPECIAL_TAX,
    ];

    /** @deprecated 請改用 $taxType->label() */
    public const TYPE_NAMES = [
        self::TAXABLE => '應稅',
        self::ZERO_TAX => '零稅率',
        self::TAX_FREE => '免稅',
        self::SPECIAL_TAX => '特種應稅',
    ];

    // ===== 方法 =====

    /**
     * 取得顯示名稱。
     */
    public function label(): string
    {
        return match ($this) {
            self::Taxable => '應稅',
            self::ZeroTax => '零稅率',
            self::TaxFree => '免稅',
            self::SpecialTax => '特種應稅',
        };
    }

    /**
     * 檢查是否為有效的課稅類別。
     */
    public static function isValid(string $type): bool
    {
        return self::tryFrom($type) !== null;
    }

    /**
     * 一般稅額發票（InvType=07）可用的類別。
     *
     * @return array<TaxType>
     */
    public static function generalInvoiceTypes(): array
    {
        return [self::Taxable, self::ZeroTax, self::TaxFree];
    }

    /**
     * 特種稅額發票（InvType=08）可用的類別。
     *
     * @return array<TaxType>
     */
    public static function specialInvoiceTypes(): array
    {
        return [self::TaxFree, self::SpecialTax];
    }

    /**
     * 檢查是否為一般稅額發票可用的類別。
     */
    public static function isValidForGeneralInvoice(string $type): bool
    {
        $enum = self::tryFrom($type);

        return $enum !== null && in_array($enum, self::generalInvoiceTypes(), true);
    }

    /**
     * 檢查是否為特種稅額發票可用的類別。
     */
    public static function isValidForSpecialInvoice(string $type): bool
    {
        $enum = self::tryFrom($type);

        return $enum !== null && in_array($enum, self::specialInvoiceTypes(), true);
    }

    /**
     * 取得類別名稱。
     *
     * @deprecated 請改用 TaxType::tryFrom($type)?->label()
     */
    public static function getName(string $type): ?string
    {
        return self::tryFrom($type)?->label();
    }
}
