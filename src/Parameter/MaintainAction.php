<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Parameter;

/**
 * 交易對象維護動作常數。
 *
 * @see https://developers.ecpay.com.tw/?p=14830
 */
final class MaintainAction
{
    /**
     * 新增。
     */
    public const ADD = 'Add';

    /**
     * 編輯。
     */
    public const UPDATE = 'Update';

    /**
     * 刪除。
     */
    public const DELETE = 'Delete';

    /**
     * 有效動作值。
     */
    public const VALID_ACTIONS = [
        self::ADD,
        self::UPDATE,
        self::DELETE,
    ];

    /**
     * 檢查是否為有效的動作值。
     *
     * @param string $action
     * @return bool
     */
    public static function isValid(string $action): bool
    {
        return in_array($action, self::VALID_ACTIONS, true);
    }
}

