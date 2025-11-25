<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Parameter;

/**
 * 確認動作常數。
 *
 * 用於開立發票確認、作廢發票確認、退回發票確認、折讓發票確認等操作。
 *
 * @see https://developers.ecpay.com.tw/?p=14855
 */
final class ConfirmAction
{
    /**
     * 確認。
     */
    public const CONFIRM = '1';

    /**
     * 退回。
     */
    public const REJECT = '2';

    /**
     * 有效動作值。
     */
    public const VALID_ACTIONS = [
        self::CONFIRM,
        self::REJECT,
    ];

    /**
     * 動作名稱對應。
     */
    public const ACTION_NAMES = [
        self::CONFIRM => '確認',
        self::REJECT => '退回',
    ];

    /**
     * 檢查是否為有效的確認動作。
     *
     * @param string $action
     * @return bool
     */
    public static function isValid(string $action): bool
    {
        return in_array($action, self::VALID_ACTIONS, true);
    }

    /**
     * 取得動作名稱。
     *
     * @param string $action
     * @return string|null
     */
    public static function getName(string $action): ?string
    {
        return self::ACTION_NAMES[$action] ?? null;
    }
}

