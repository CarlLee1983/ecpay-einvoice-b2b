<?php

declare(strict_types=1);

use CarlLee\EcPayB2B\Operations\RejectConfirm;
use CarlLee\EcPayB2B\Parameter\ConfirmAction;
use PHPUnit\Framework\TestCase;

class RejectConfirmTest extends TestCase
{
    private RejectConfirm $operation;

    protected function setUp(): void
    {
        $this->operation = new RejectConfirm(
            getenv('MERCHANT_ID') ?: '2000132',
            getenv('HASH_KEY') ?: 'ejCk326UnaZWKisg',
            getenv('HASH_IV') ?: 'q9jcZX8Ib9LM8wYk'
        );
    }

    public function testRequestPath(): void
    {
        $this->assertEquals('/B2BInvoice/RejectConfirm', $this->operation->getRequestPath());
    }

    // InvoiceNumber tests
    public function testInvoiceNumberRequired(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceNumber cannot be empty.');

        $this->operation
            ->setInvoiceDate('2024-01-01')
            ->confirm()
            ->getContent();
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
            ->setInvoiceNumber('AB12345678')
            ->confirm()
            ->getContent();
    }

    public function testSetInvoiceDate(): void
    {
        $this->operation->setInvoiceDate('2024-01-15');
        $content = $this->getRawContent();

        $this->assertSame('2024-01-15', $content['Data']['InvoiceDate']);
    }

    // ConfirmAction tests
    public function testConfirm(): void
    {
        $this->operation->confirm();
        $content = $this->getRawContent();

        $this->assertSame('1', $content['Data']['ConfirmAction']);
    }

    public function testReject(): void
    {
        $this->operation->reject('拒絕退回原因');
        $content = $this->getRawContent();

        $this->assertSame('2', $content['Data']['ConfirmAction']);
        $this->assertSame('拒絕退回原因', $content['Data']['RejectReason']);
    }

    // Full payload test
    public function testFullPayload(): void
    {
        $content = $this->operation
            ->setInvoiceNumber('AB12345678')
            ->setInvoiceDate('2024-01-15')
            ->confirm()
            ->getContent();

        $this->assertArrayHasKey('Data', $content);
        $this->assertArrayHasKey('MerchantID', $content);
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

