<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Parameter;

/**
 * 開立形式。
 *
 * @see https://developers.ecpay.com.tw/?p=14830
 */
enum ExchangeMode: string
{
    /**
     * 存證模式。
     *
     * 綠界僅會將發票資料上傳至財政部，僅適用於銷項發票。
     * 加值中心無法接收其他營業人開立給您的電子發票。
     */
    case Archive = '0';

    /**
     * 交換模式。
     *
     * 綠界會將發票資料上傳至財政部發票傳輸軟體供對方營業人確認及接收。
     * 請務必先至財政部平台設定由綠界接收。
     */
    case Exchange = '1';

    // ===== 向後相容常數（標記 @deprecated）=====

    /** @deprecated 請改用 ExchangeMode::Archive */
    public const ARCHIVE = '0';

    /** @deprecated 請改用 ExchangeMode::Exchange */
    public const EXCHANGE = '1';

    /** @deprecated 請改用 ExchangeMode::cases() */
    public const VALID_MODES = [
        self::ARCHIVE,
        self::EXCHANGE,
    ];

    /** @deprecated 請改用 $mode->label() */
    public const MODE_NAMES = [
        self::ARCHIVE => '存證',
        self::EXCHANGE => '交換',
    ];

    // ===== 方法 =====

    /**
     * 取得顯示名稱。
     */
    public function label(): string
    {
        return match ($this) {
            self::Archive => '存證',
            self::Exchange => '交換',
        };
    }

    /**
     * 檢查是否為有效的模式值。
     */
    public static function isValid(string $mode): bool
    {
        return self::tryFrom($mode) !== null;
    }

    /**
     * 取得模式名稱。
     *
     * @deprecated 請改用 ExchangeMode::tryFrom($mode)?->label()
     */
    public static function getName(string $mode): ?string
    {
        return self::tryFrom($mode)?->label();
    }
}
