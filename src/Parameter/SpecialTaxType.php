<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Parameter;

/**
 * 特種稅額類別常數。
 *
 * 當課稅類別 TaxType 為 4（特種應稅）時，此參數必填。
 *
 * @see https://developers.ecpay.com.tw/?p=14850
 */
final class SpecialTaxType
{
    /**
     * 夜總會、有乘人服務之茶室、咖啡廳、酒家及酒吧 - 稅率 25%。
     */
    public const NIGHTCLUB_25 = '1';

    /**
     * 高爾夫球場 - 稅率 10%。
     */
    public const GOLF_10 = '2';

    /**
     * 保齡球館 - 稅率 15%。
     */
    public const BOWLING_15 = '3';

    /**
     * 撞球場 - 稅率 15%。
     */
    public const BILLIARDS_15 = '4';

    /**
     * 其他（限銀行業、保險業、證券期貨金融業等） - 稅率 2%。
     *
     * 銀行業、保險業、信託投資業、證券業、期貨業、票券業及典當業
     * 僅就非專屬本業之銷售額課徵特種營業稅。
     */
    public const FINANCIAL_2 = '5';

    /**
     * 有效類別值。
     */
    public const VALID_TYPES = [
        self::NIGHTCLUB_25,
        self::GOLF_10,
        self::BOWLING_15,
        self::BILLIARDS_15,
        self::FINANCIAL_2,
    ];

    /**
     * 類別對應稅率。
     */
    public const TAX_RATES = [
        self::NIGHTCLUB_25 => 0.25,
        self::GOLF_10 => 0.10,
        self::BOWLING_15 => 0.15,
        self::BILLIARDS_15 => 0.15,
        self::FINANCIAL_2 => 0.02,
    ];

    /**
     * 類別名稱對應。
     */
    public const TYPE_NAMES = [
        self::NIGHTCLUB_25 => '夜總會、有乘人服務之茶室、咖啡廳、酒家及酒吧（25%）',
        self::GOLF_10 => '高爾夫球場（10%）',
        self::BOWLING_15 => '保齡球館（15%）',
        self::BILLIARDS_15 => '撞球場（15%）',
        self::FINANCIAL_2 => '銀行業、保險業、證券業等（2%）',
    ];

    /**
     * 檢查是否為有效的特種稅額類別。
     *
     * @param string $type
     * @return bool
     */
    public static function isValid(string $type): bool
    {
        return in_array($type, self::VALID_TYPES, true);
    }

    /**
     * 取得類別對應的稅率。
     *
     * @param string $type
     * @return float|null
     */
    public static function getTaxRate(string $type): ?float
    {
        return self::TAX_RATES[$type] ?? null;
    }

    /**
     * 取得類別名稱。
     *
     * @param string $type
     * @return string|null
     */
    public static function getName(string $type): ?string
    {
        return self::TYPE_NAMES[$type] ?? null;
    }
}

