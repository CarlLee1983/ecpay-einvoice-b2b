<?php

declare(strict_types=1);

namespace ecPay\eInvoiceB2B\Parameter;

/**
 * 開立形式常數。
 *
 * @see https://developers.ecpay.com.tw/?p=14830
 */
final class ExchangeMode
{
    /**
     * 存證模式。
     *
     * 綠界僅會將發票資料上傳至財政部，僅適用於銷項發票。
     * 加值中心無法接收其他營業人開立給您的電子發票。
     */
    public const ARCHIVE = '0';

    /**
     * 交換模式。
     *
     * 綠界會將發票資料上傳至財政部發票傳輸軟體供對方營業人確認及接收。
     * 請務必先至財政部平台設定由綠界接收。
     */
    public const EXCHANGE = '1';

    /**
     * 有效模式值。
     */
    public const VALID_MODES = [
        self::ARCHIVE,
        self::EXCHANGE,
    ];

    /**
     * 模式名稱對應。
     */
    public const MODE_NAMES = [
        self::ARCHIVE => '存證',
        self::EXCHANGE => '交換',
    ];

    /**
     * 檢查是否為有效的模式值。
     *
     * @param string $mode
     * @return bool
     */
    public static function isValid(string $mode): bool
    {
        return in_array($mode, self::VALID_MODES, true);
    }

    /**
     * 取得模式名稱。
     *
     * @param string $mode
     * @return string|null
     */
    public static function getName(string $mode): ?string
    {
        return self::MODE_NAMES[$mode] ?? null;
    }
}

