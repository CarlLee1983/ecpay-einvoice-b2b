<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Parameter;

/**
 * 交易對象維護動作。
 *
 * @see https://developers.ecpay.com.tw/?p=14830
 */
enum MaintainAction: string
{
    /**
     * 新增。
     */
    case Add = 'Add';

    /**
     * 編輯。
     */
    case Update = 'Update';

    /**
     * 刪除。
     */
    case Delete = 'Delete';

    // ===== 向後相容常數（標記 @deprecated）=====

    /** @deprecated 請改用 MaintainAction::Add */
    public const string ADD = 'Add';

    /** @deprecated 請改用 MaintainAction::Update */
    public const string UPDATE = 'Update';

    /** @deprecated 請改用 MaintainAction::Delete */
    public const string DELETE = 'Delete';

    /** @deprecated 請改用 MaintainAction::cases() */
    public const VALID_ACTIONS = [
        self::ADD,
        self::UPDATE,
        self::DELETE,
    ];

    // ===== 方法 =====

    /**
     * 取得顯示名稱。
     */
    public function label(): string
    {
        return match ($this) {
            self::Add => '新增',
            self::Update => '編輯',
            self::Delete => '刪除',
        };
    }

    /**
     * 檢查是否為有效的動作值。
     */
    public static function isValid(string $action): bool
    {
        return self::tryFrom($action) !== null;
    }
}
