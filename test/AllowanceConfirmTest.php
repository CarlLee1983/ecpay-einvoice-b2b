<?php

declare(strict_types=1);

use ecPay\eInvoiceB2B\Operations\AllowanceConfirm;
use ecPay\eInvoiceB2B\Parameter\ConfirmAction;
use PHPUnit\Framework\TestCase;

class AllowanceConfirmTest extends TestCase
{
    private AllowanceConfirm $operation;

    protected function setUp(): void
    {
        $this->operation = new AllowanceConfirm(
            getenv('MERCHANT_ID') ?: '2000132',
            getenv('HASH_KEY') ?: 'ejCk326UnaZWKisg',
            getenv('HASH_IV') ?: 'q9jcZX8Ib9LM8wYk'
        );
    }

    public function testRequestPath(): void
    {
        $this->assertEquals('/B2BInvoice/AllowanceConfirm', $this->operation->getRequestPath());
    }

    // AllowanceNumber tests
    public function testAllowanceNumberRequired(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('AllowanceNumber cannot be empty.');

        $this->operation
            ->setAllowanceDate('2024-01-01')
            ->confirm()
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

    public function testAllowanceNumberUppercase(): void
    {
        $this->operation->setAllowanceNumber('ab123456780001');
        $content = $this->getRawContent();

        $this->assertSame('AB123456780001', $content['Data']['AllowanceNumber']);
    }

    // AllowanceDate tests
    public function testAllowanceDateRequired(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('AllowanceDate cannot be empty.');

        $this->operation
            ->setAllowanceNumber('AB123456780001')
            ->confirm()
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

    // ConfirmAction tests
    public function testConfirm(): void
    {
        $this->operation->confirm();
        $content = $this->getRawContent();

        $this->assertSame('1', $content['Data']['ConfirmAction']);
    }

    public function testReject(): void
    {
        $this->operation->reject('退回折讓原因');
        $content = $this->getRawContent();

        $this->assertSame('2', $content['Data']['ConfirmAction']);
        $this->assertSame('退回折讓原因', $content['Data']['RejectReason']);
    }

    // Full payload test
    public function testFullPayload(): void
    {
        $content = $this->operation
            ->setAllowanceNumber('AB123456780001')
            ->setAllowanceDate('2024-01-15')
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

