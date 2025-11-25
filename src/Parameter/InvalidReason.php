<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Parameter;

/**
 * 作廢原因。
 *
 * 發票作廢或折讓作廢時使用的原因代碼。
 * 此為自定義欄位，可依實際需求調整原因內容。
 *
 * @see https://developers.ecpay.com.tw/?p=14860
 */
enum InvalidReason: string
{
    /**
     * 發票開立錯誤。
     */
    case InvoiceError = '發票開立錯誤';

    /**
     * 銷貨退回。
     */
    case SalesReturn = '銷貨退回';

    /**
     * 折讓錯誤。
     */
    case AllowanceError = '折讓錯誤';

    /**
     * 金額錯誤。
     */
    case AmountError = '金額錯誤';

    /**
     * 重複開立。
     */
    case DuplicateIssue = '重複開立';

    /**
     * 買方資料錯誤。
     */
    case BuyerDataError = '買方資料錯誤';

    /**
     * 商品資料錯誤。
     */
    case ItemDataError = '商品資料錯誤';

    /**
     * 客戶要求作廢。
     */
    case CustomerRequest = '客戶要求作廢';

    /**
     * 其他原因。
     */
    case Other = '其他';

    // ===== 向後相容常數（標記 @deprecated）=====

    /** @deprecated 請改用 InvalidReason::InvoiceError */
    public const string INVOICE_ERROR = '發票開立錯誤';

    /** @deprecated 請改用 InvalidReason::SalesReturn */
    public const string SALES_RETURN = '銷貨退回';

    /** @deprecated 請改用 InvalidReason::AllowanceError */
    public const string ALLOWANCE_ERROR = '折讓錯誤';

    /** @deprecated 請改用 InvalidReason::AmountError */
    public const string AMOUNT_ERROR = '金額錯誤';

    /** @deprecated 請改用 InvalidReason::DuplicateIssue */
    public const string DUPLICATE_ISSUE = '重複開立';

    /** @deprecated 請改用 InvalidReason::BuyerDataError */
    public const string BUYER_DATA_ERROR = '買方資料錯誤';

    /** @deprecated 請改用 InvalidReason::ItemDataError */
    public const string ITEM_DATA_ERROR = '商品資料錯誤';

    /** @deprecated 請改用 InvalidReason::CustomerRequest */
    public const string CUSTOMER_REQUEST = '客戶要求作廢';

    /** @deprecated 請改用 InvalidReason::Other */
    public const string OTHER = '其他';

    /** @deprecated 請改用 InvalidReason::cases() */
    public const COMMON_REASONS = [
        self::INVOICE_ERROR,
        self::SALES_RETURN,
        self::ALLOWANCE_ERROR,
        self::AMOUNT_ERROR,
        self::DUPLICATE_ISSUE,
        self::BUYER_DATA_ERROR,
        self::ITEM_DATA_ERROR,
        self::CUSTOMER_REQUEST,
        self::OTHER,
    ];

    // ===== 方法 =====

    /**
     * 取得顯示名稱（與值相同）。
     */
    public function label(): string
    {
        return $this->value;
    }

    /**
     * 檢查是否為預設的作廢原因。
     */
    public static function isCommonReason(string $reason): bool
    {
        return self::tryFrom($reason) !== null;
    }
}
