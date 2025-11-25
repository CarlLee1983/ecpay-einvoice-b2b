<?php

declare(strict_types=1);

use ecPay\eInvoiceB2B\Operations\Reject;
use ecPay\eInvoiceB2B\Parameter\B2BInvoiceCategory;
use PHPUnit\Framework\TestCase;

class RejectTest extends TestCase
{
    private Reject $operation;

    protected function setUp(): void
    {
        $this->operation = new Reject(
            getenv('MERCHANT_ID') ?: '2000132',
            getenv('HASH_KEY') ?: 'ejCk326UnaZWKisg',
            getenv('HASH_IV') ?: 'q9jcZX8Ib9LM8wYk'
        );
    }

    public function testRequestPath(): void
    {
        $this->assertEquals('/B2BInvoice/Reject', $this->operation->getRequestPath());
    }

    // InvoiceCategory tests
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
            ->setRejectReason('測試退回')
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
            ->salesInvoice()
            ->setInvoiceNumber('AB12345678')
            ->setRejectReason('測試退回')
            ->getContent();
    }

    public function testSetInvoiceDate(): void
    {
        $this->operation->setInvoiceDate('2024-01-15');
        $content = $this->getRawContent();

        $this->assertSame('2024-01-15', $content['Data']['InvoiceDate']);
    }

    // RejectReason tests
    public function testRejectReasonRequired(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('RejectReason cannot be empty.');

        $this->operation
            ->salesInvoice()
            ->setInvoiceNumber('AB12345678')
            ->setInvoiceDate('2024-01-01')
            ->getContent();
    }

    public function testRejectReasonEmpty(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('RejectReason cannot be empty.');

        $this->operation->setRejectReason('');
    }

    public function testRejectReasonMaxLength(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('RejectReason cannot exceed 200 characters.');

        $this->operation->setRejectReason(str_repeat('A', 201));
    }

    public function testSetRejectReason(): void
    {
        $this->operation->setRejectReason('發票內容錯誤');
        $content = $this->getRawContent();

        $this->assertSame('發票內容錯誤', $content['Data']['RejectReason']);
    }

    // Full payload test
    public function testFullPayload(): void
    {
        $content = $this->operation
            ->salesInvoice()
            ->setInvoiceNumber('AB12345678')
            ->setInvoiceDate('2024-01-15')
            ->setRejectReason('測試退回原因')
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

