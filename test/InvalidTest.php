<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Tests;

use Exception;
use ReflectionClass;
use CarlLee\EcPayB2B\Operations\Invalid;
use CarlLee\EcPayB2B\Parameter\B2BInvoiceCategory;
use CarlLee\EcPayB2B\Parameter\InvalidReason;
use PHPUnit\Framework\TestCase;

class InvalidTest extends TestCase
{
    private Invalid $operation;

    protected function setUp(): void
    {
        $this->operation = new Invalid(
            getenv('MERCHANT_ID') ?: '2000132',
            getenv('HASH_KEY') ?: 'ejCk326UnaZWKisg',
            getenv('HASH_IV') ?: 'q9jcZX8Ib9LM8wYk'
        );
    }

    public function testRequestPath(): void
    {
        $this->assertEquals('/B2BInvoice/Invalid', $this->operation->getRequestPath());
    }

    // InvoiceCategory tests
    public function testInvalidInvoiceCategory(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceCategory must be 0 (銷項發票) or 1 (進項發票).');

        $this->operation->setInvoiceCategory(2);
    }

    public function testSalesInvoice(): void
    {
        $this->operation->salesInvoice();
        $content = $this->getRawContent();

        $this->assertSame(0, $content['Data']['InvoiceCategory']);
    }

    public function testPurchaseInvoice(): void
    {
        $this->operation->purchaseInvoice();
        $content = $this->getRawContent();

        $this->assertSame(1, $content['Data']['InvoiceCategory']);
    }

    // InvoiceNumber tests
    public function testInvoiceNumberRequired(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceNumber cannot be empty.');

        $this->operation
            ->salesInvoice()
            ->setInvoiceDate('2024-01-01')
            ->setInvalidReason('測試作廢')
            ->getContent();
    }

    public function testInvoiceNumberFormat(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceNumber must be 2 letters followed by 8 digits');

        $this->operation->setInvoiceNumber('1234567890');
    }

    public function testSetInvoiceNumber(): void
    {
        $this->operation->setInvoiceNumber('AB12345678');
        $content = $this->getRawContent();

        $this->assertSame('AB12345678', $content['Data']['InvoiceNumber']);
    }

    // InvoiceDate tests
    public function testInvoiceDateRequired(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceDate cannot be empty.');

        $this->operation
            ->salesInvoice()
            ->setInvoiceNumber('AB12345678')
            ->setInvalidReason('測試作廢')
            ->getContent();
    }

    public function testInvoiceDateInvalidFormat(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceDate must be in yyyy-mm-dd format.');

        $this->operation->setInvoiceDate('2024/01/01');
    }

    public function testSetInvoiceDate(): void
    {
        $this->operation->setInvoiceDate('2024-01-15');
        $content = $this->getRawContent();

        $this->assertSame('2024-01-15', $content['Data']['InvoiceDate']);
    }

    // InvalidReason tests
    public function testInvalidReasonRequired(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvalidReason cannot be empty.');

        $this->operation
            ->salesInvoice()
            ->setInvoiceNumber('AB12345678')
            ->setInvoiceDate('2024-01-01')
            ->getContent();
    }

    public function testInvalidReasonEmpty(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvalidReason cannot be empty.');

        $this->operation->setInvalidReason('');
    }

    public function testInvalidReasonMaxLength(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvalidReason cannot exceed 200 characters.');

        $this->operation->setInvalidReason(str_repeat('A', 201));
    }

    public function testSetInvalidReason(): void
    {
        $this->operation->setInvalidReason(InvalidReason::INVOICE_ERROR);
        $content = $this->getRawContent();

        $this->assertSame('發票開立錯誤', $content['Data']['InvalidReason']);
    }

    // Full payload test
    public function testFullPayload(): void
    {
        $content = $this->operation
            ->salesInvoice()
            ->setInvoiceNumber('AB12345678')
            ->setInvoiceDate('2024-01-15')
            ->setInvalidReason('測試作廢原因')
            ->getContent();

        $this->assertArrayHasKey('Data', $content);
        $this->assertArrayHasKey('MerchantID', $content);
        $this->assertArrayHasKey('RqHeader', $content);
    }

    /**
     * 取得未加密的原始內容。
     */
    private function getRawContent(): array
    {
        $reflection = new ReflectionClass($this->operation);
        $property = $reflection->getProperty('content');
        $property->setAccessible(true);

        return $property->getValue($this->operation);
    }
}
