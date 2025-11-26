<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Tests;

use Exception;
use ReflectionClass;
use CarlLee\EcPayB2B\Operations\IssueConfirm;
use CarlLee\EcPayB2B\Parameter\ConfirmAction;
use PHPUnit\Framework\TestCase;

class IssueConfirmTest extends TestCase
{
    private IssueConfirm $operation;

    protected function setUp(): void
    {
        $this->operation = new IssueConfirm(
            getenv('MERCHANT_ID') ?: '2000132',
            getenv('HASH_KEY') ?: 'ejCk326UnaZWKisg',
            getenv('HASH_IV') ?: 'q9jcZX8Ib9LM8wYk'
        );
    }

    public function testRequestPath(): void
    {
        $this->assertEquals('/B2BInvoice/IssueConfirm', $this->operation->getRequestPath());
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

    public function testInvoiceNumberUppercase(): void
    {
        $this->operation->setInvoiceNumber('ab12345678');
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

    // ConfirmAction tests
    public function testSetConfirmAction(): void
    {
        $this->operation->setConfirmAction(ConfirmAction::CONFIRM);
        $content = $this->getRawContent();

        $this->assertSame('1', $content['Data']['ConfirmAction']);
    }

    public function testInvalidConfirmAction(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('ConfirmAction must be 1 (確認) or 2 (退回).');

        $this->operation->setConfirmAction('3');
    }

    public function testConfirm(): void
    {
        $this->operation->confirm();
        $content = $this->getRawContent();

        $this->assertSame('1', $content['Data']['ConfirmAction']);
    }

    public function testReject(): void
    {
        $this->operation->reject('測試退回原因');
        $content = $this->getRawContent();

        $this->assertSame('2', $content['Data']['ConfirmAction']);
        $this->assertSame('測試退回原因', $content['Data']['RejectReason']);
    }

    // RejectReason tests
    public function testRejectReasonMaxLength(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('RejectReason cannot exceed 200 characters.');

        $this->operation->setRejectReason(str_repeat('A', 201));
    }

    public function testSetRejectReason(): void
    {
        $this->operation->setRejectReason('退回原因說明');
        $content = $this->getRawContent();

        $this->assertSame('退回原因說明', $content['Data']['RejectReason']);
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
