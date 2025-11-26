<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Factories;

use CarlLee\EcPay\Core\Contracts\OperationFactoryInterface as CoreOperationFactoryInterface;

/**
 * B2B 操作工廠介面。
 *
 * 繼承自 Core 的 OperationFactoryInterface，保持向下相容。
 */
interface OperationFactoryInterface extends CoreOperationFactoryInterface
{
}
