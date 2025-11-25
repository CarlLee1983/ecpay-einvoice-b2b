<?php

declare(strict_types=1);

use CarlLee\EcPayB2B\Operations\AllowanceInvalid;
use CarlLee\EcPayB2B\Parameter\B2BInvoiceCategory;
use CarlLee\EcPayB2B\Parameter\InvalidReason;
use PHPUnit\Framework\TestCase;

class AllowanceInvalidTest extends TestCase
{
    private AllowanceInvalid $operation;

    protected function setUp(): void
    {
        $this->operation = new AllowanceInvalid(
            getenv('MERCHANT_ID') ?: '2000132',
            getenv('HASH_KEY') ?: 'ejCk326UnaZWKisg',
            getenv('HASH_IV') ?: 'q9jcZX8Ib9LM8wYk'
        );
    }

    public function testRequestPath(): void
    {
        $this->assertEquals('/B2BInvoice/AllowanceInvalid', $this->operation->getRequestPath());
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

    // AllowanceNumber tests
    public function testAllowanceNumberRequired(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('AllowanceNumber cannot be empty.');

        $this->operation
            ->salesInvoice()
            ->setAllowanceDate('2024-01-01')
            ->setInvalidReason('測試作廢')
            ->getContent();
    }

    public function testAllowanceNumberFormat(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('AllowanceNumber must be 14 characters');

        $this->operation->setAllowanceNumber('AB12345678');
    }

    public function testSetAllowanceNumber(): void
    {
        $this->operation->setAllowanceNumber('AB123456780001');
        $content = $this->getRawContent();

        $this->assertSame('AB123456780001', $content['Data']['AllowanceNumber']);
    }

    // AllowanceDate tests
    public function testAllowanceDateRequired(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('AllowanceDate cannot be empty.');

        $this->operation
            ->salesInvoice()
            ->setAllowanceNumber('AB123456780001')
            ->setInvalidReason('測試作廢')
            ->getContent();
    }

    public function testAllowanceDateInvalidFormat(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('AllowanceDate must be in yyyy-mm-dd format.');

        $this->operation->setAllowanceDate('2024/01/01');
    }

    public function testSetAllowanceDate(): void
    {
        $this->operation->setAllowanceDate('2024-01-15');
        $content = $this->getRawContent();

        $this->assertSame('2024-01-15', $content['Data']['AllowanceDate']);
    }

    // InvalidReason tests
    public function testInvalidReasonRequired(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvalidReason cannot be empty.');

        $this->operation
            ->salesInvoice()
            ->setAllowanceNumber('AB123456780001')
            ->setAllowanceDate('2024-01-01')
            ->getContent();
    }

    public function testInvalidReasonEmpty(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvalidReason cannot be empty.');

        $this->operation->setInvalidReason('');
    }

    public function testSetInvalidReason(): void
    {
        $this->operation->setInvalidReason(InvalidReason::ALLOWANCE_ERROR);
        $content = $this->getRawContent();

        $this->assertSame('折讓錯誤', $content['Data']['InvalidReason']);
    }

    // Full payload test
    public function testFullPayload(): void
    {
        $content = $this->operation
            ->salesInvoice()
            ->setAllowanceNumber('AB123456780001')
            ->setAllowanceDate('2024-01-15')
            ->setInvalidReason('測試作廢原因')
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

