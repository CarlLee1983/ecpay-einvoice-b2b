<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Factories;

use CarlLee\EcPay\Core\AbstractOperationFactory;
use CarlLee\EcPayB2B\Content;

/**
 * B2B 電子發票操作工廠。
 *
 * 繼承自 Core 的 AbstractOperationFactory，
 * 提供 B2B 套件特定的命名空間配置。
 */
class OperationFactory extends AbstractOperationFactory implements OperationFactoryInterface
{
    /**
     * @inheritDoc
     */
    protected function getBaseNamespace(): string
    {
        return 'CarlLee\\EcPayB2B';
    }

    /**
     * @inheritDoc
     */
    protected function getContentClass(): string
    {
        return Content::class;
    }
}
