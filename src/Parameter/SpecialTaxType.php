<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Parameter;

/**
 * 特種稅額類別。
 *
 * 當課稅類別 TaxType 為 4（特種應稅）時，此參數必填。
 *
 * @see https://developers.ecpay.com.tw/?p=14850
 */
enum SpecialTaxType: string
{
    /**
     * 夜總會、有乘人服務之茶室、咖啡廳、酒家及酒吧 - 稅率 25%。
     */
    case Nightclub25 = '1';

    /**
     * 高爾夫球場 - 稅率 10%。
     */
    case Golf10 = '2';

    /**
     * 保齡球館 - 稅率 15%。
     */
    case Bowling15 = '3';

    /**
     * 撞球場 - 稅率 15%。
     */
    case Billiards15 = '4';

    /**
     * 其他（限銀行業、保險業、證券期貨金融業等） - 稅率 2%。
     *
     * 銀行業、保險業、信託投資業、證券業、期貨業、票券業及典當業
     * 僅就非專屬本業之銷售額課徵特種營業稅。
     */
    case Financial2 = '5';

    // ===== 向後相容常數（標記 @deprecated）=====

    /** @deprecated 請改用 SpecialTaxType::Nightclub25 */
    public const NIGHTCLUB_25 = '1';

    /** @deprecated 請改用 SpecialTaxType::Golf10 */
    public const GOLF_10 = '2';

    /** @deprecated 請改用 SpecialTaxType::Bowling15 */
    public const BOWLING_15 = '3';

    /** @deprecated 請改用 SpecialTaxType::Billiards15 */
    public const BILLIARDS_15 = '4';

    /** @deprecated 請改用 SpecialTaxType::Financial2 */
    public const FINANCIAL_2 = '5';

    /** @deprecated 請改用 SpecialTaxType::cases() */
    public const VALID_TYPES = [
        self::NIGHTCLUB_25,
        self::GOLF_10,
        self::BOWLING_15,
        self::BILLIARDS_15,
        self::FINANCIAL_2,
    ];

    /** @deprecated 請改用 $type->taxRate() */
    public const TAX_RATES = [
        self::NIGHTCLUB_25 => 0.25,
        self::GOLF_10 => 0.10,
        self::BOWLING_15 => 0.15,
        self::BILLIARDS_15 => 0.15,
        self::FINANCIAL_2 => 0.02,
    ];

    /** @deprecated 請改用 $type->label() */
    public const TYPE_NAMES = [
        self::NIGHTCLUB_25 => '夜總會、有乘人服務之茶室、咖啡廳、酒家及酒吧（25%）',
        self::GOLF_10 => '高爾夫球場（10%）',
        self::BOWLING_15 => '保齡球館（15%）',
        self::BILLIARDS_15 => '撞球場（15%）',
        self::FINANCIAL_2 => '銀行業、保險業、證券業等（2%）',
    ];

    // ===== 方法 =====

    /**
     * 取得顯示名稱。
     */
    public function label(): string
    {
        return match ($this) {
            self::Nightclub25 => '夜總會、有乘人服務之茶室、咖啡廳、酒家及酒吧（25%）',
            self::Golf10 => '高爾夫球場（10%）',
            self::Bowling15 => '保齡球館（15%）',
            self::Billiards15 => '撞球場（15%）',
            self::Financial2 => '銀行業、保險業、證券業等（2%）',
        };
    }

    /**
     * 取得稅率。
     */
    public function taxRate(): float
    {
        return match ($this) {
            self::Nightclub25 => 0.25,
            self::Golf10 => 0.10,
            self::Bowling15 => 0.15,
            self::Billiards15 => 0.15,
            self::Financial2 => 0.02,
        };
    }

    /**
     * 取得稅率百分比（例如 25, 10, 15, 2）。
     */
    public function taxRatePercent(): int
    {
        return (int) ($this->taxRate() * 100);
    }

    /**
     * 檢查是否為有效的特種稅額類別。
     */
    public static function isValid(string $type): bool
    {
        return self::tryFrom($type) !== null;
    }

    /**
     * 取得類別對應的稅率。
     *
     * @deprecated 請改用 SpecialTaxType::tryFrom($type)?->taxRate()
     */
    public static function getTaxRate(string $type): ?float
    {
        return self::tryFrom($type)?->taxRate();
    }

    /**
     * 取得類別名稱。
     *
     * @deprecated 請改用 SpecialTaxType::tryFrom($type)?->label()
     */
    public static function getName(string $type): ?string
    {
        return self::tryFrom($type)?->label();
    }
}
