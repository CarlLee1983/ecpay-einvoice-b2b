<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Parameter;

/**
 * 確認動作。
 *
 * 用於開立發票確認、作廢發票確認、退回發票確認、折讓發票確認等操作。
 *
 * @see https://developers.ecpay.com.tw/?p=14855
 */
enum ConfirmAction: string
{
    /**
     * 確認。
     */
    case Confirm = '1';

    /**
     * 退回。
     */
    case Reject = '2';

    // ===== 向後相容常數（標記 @deprecated）=====

    /** @deprecated 請改用 ConfirmAction::Confirm */
    public const CONFIRM = '1';

    /** @deprecated 請改用 ConfirmAction::Reject */
    public const REJECT = '2';

    /** @deprecated 請改用 ConfirmAction::cases() */
    public const VALID_ACTIONS = [
        self::CONFIRM,
        self::REJECT,
    ];

    /** @deprecated 請改用 $action->label() */
    public const ACTION_NAMES = [
        self::CONFIRM => '確認',
        self::REJECT => '退回',
    ];

    // ===== 方法 =====

    /**
     * 取得顯示名稱。
     */
    public function label(): string
    {
        return match ($this) {
            self::Confirm => '確認',
            self::Reject => '退回',
        };
    }

    /**
     * 檢查是否為有效的確認動作。
     */
    public static function isValid(string $action): bool
    {
        return self::tryFrom($action) !== null;
    }

    /**
     * 取得動作名稱。
     *
     * @deprecated 請改用 ConfirmAction::tryFrom($action)?->label()
     */
    public static function getName(string $action): ?string
    {
        return self::tryFrom($action)?->label();
    }
}
