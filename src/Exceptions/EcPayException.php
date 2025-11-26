<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Exceptions;

use CarlLee\EcPay\Core\Exceptions\EcPayException as CoreEcPayException;

/**
 * 綠界電子發票 B2B API 基礎例外類別。
 *
 * 繼承自 Core 的 EcPayException，保持向下相容。
 */
class EcPayException extends CoreEcPayException
{
}
