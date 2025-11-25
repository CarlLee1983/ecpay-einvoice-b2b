<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Parameter;

/**
 * 作廢原因常數。
 *
 * 發票作廢或折讓作廢時使用的原因代碼。
 * 此為自定義欄位，可依實際需求調整原因內容。
 *
 * @see https://developers.ecpay.com.tw/?p=14860
 */
final class InvalidReason
{
    /**
     * 發票開立錯誤。
     */
    public const INVOICE_ERROR = '發票開立錯誤';

    /**
     * 銷貨退回。
     */
    public const SALES_RETURN = '銷貨退回';

    /**
     * 折讓錯誤。
     */
    public const ALLOWANCE_ERROR = '折讓錯誤';

    /**
     * 金額錯誤。
     */
    public const AMOUNT_ERROR = '金額錯誤';

    /**
     * 重複開立。
     */
    public const DUPLICATE_ISSUE = '重複開立';

    /**
     * 買方資料錯誤。
     */
    public const BUYER_DATA_ERROR = '買方資料錯誤';

    /**
     * 商品資料錯誤。
     */
    public const ITEM_DATA_ERROR = '商品資料錯誤';

    /**
     * 客戶要求作廢。
     */
    public const CUSTOMER_REQUEST = '客戶要求作廢';

    /**
     * 其他原因。
     */
    public const OTHER = '其他';

    /**
     * 常用作廢原因列表。
     */
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
}

