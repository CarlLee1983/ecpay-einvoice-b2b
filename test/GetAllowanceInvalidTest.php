<?php

declare(strict_types=1);

use CarlLee\EcPayB2B\Parameter\B2BInvoiceCategory;
use CarlLee\EcPayB2B\Queries\GetAllowanceInvalid;
use PHPUnit\Framework\TestCase;

class GetAllowanceInvalidTest extends TestCase
{
    private GetAllowanceInvalid $query;

    protected function setUp(): void
    {
        $this->query = new GetAllowanceInvalid(
            getenv('MERCHANT_ID') ?: '2000132',
            getenv('HASH_KEY') ?: 'ejCk326UnaZWKisg',
            getenv('HASH_IV') ?: 'q9jcZX8Ib9LM8wYk'
        );
    }

    public function testRequestPath(): void
    {
        $this->assertEquals('/B2BInvoice/GetAllowanceInvalid', $this->query->getRequestPath());
    }

    // InvoiceCategory tests
    public function testInvoiceCategoryRequired(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceCategory cannot be empty.');

        $this->query
            ->setAllowanceNumber('AB123456780001')
            ->setAllowanceDate('2024-01-01')
            ->getContent();
    }

    public function testInvalidInvoiceCategory(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceCategory must be 0 (銷項發票) or 1 (進項發票).');

        $this->query->setInvoiceCategory(2);
    }

    public function testSalesInvoiceMethod(): void
    {
        $this->query->salesInvoice();
        $content = $this->getRawContent();

        $this->assertSame(0, $content['Data']['InvoiceCategory']);
    }

    public function testPurchaseInvoiceMethod(): void
    {
        $this->query->purchaseInvoice();
        $content = $this->getRawContent();

        $this->assertSame(1, $content['Data']['InvoiceCategory']);
    }

    // AllowanceNumber / RelateNumber required tests
    public function testEitherAllowanceNumberOrRelateNumberRequired(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Either AllowanceNumber or RelateNumber must be provided.');

        $this->query
            ->salesInvoice()
            ->getContent();
    }

    public function testAllowanceDateRequiredWhenAllowanceNumberProvided(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('AllowanceDate is required when AllowanceNumber is provided.');

        $this->query
            ->salesInvoice()
            ->setAllowanceNumber('AB123456780001')
            ->getContent();
    }

    public function testCanQueryByRelateNumberOnly(): void
    {
        $content = $this->query
            ->salesInvoice()
            ->setRelateNumber('ALLOWANCE2024001')
            ->getContent();

        $this->assertArrayHasKey('Data', $content);
    }

    // AllowanceNumber tests
    public function testAllowanceNumberFormat(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('AllowanceNumber must be 14 characters');

        $this->query->setAllowanceNumber('1234567890');
    }

    public function testSetAllowanceNumber(): void
    {
        $this->query->setAllowanceNumber('AB123456780001');
        $content = $this->getRawContent();

        $this->assertSame('AB123456780001', $content['Data']['AllowanceNumber']);
    }

    public function testAllowanceNumberUppercase(): void
    {
        $this->query->setAllowanceNumber('ab123456780001');
        $content = $this->getRawContent();

        $this->assertSame('AB123456780001', $content['Data']['AllowanceNumber']);
    }

    // AllowanceDate tests
    public function testAllowanceDateInvalidFormat(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('AllowanceDate must be in yyyy-mm-dd format.');

        $this->query->setAllowanceDate('2024/01/01');
    }

    public function testSetAllowanceDate(): void
    {
        $this->query->setAllowanceDate('2024-01-15');
        $content = $this->getRawContent();

        $this->assertSame('2024-01-15', $content['Data']['AllowanceDate']);
    }

    // RelateNumber tests
    public function testRelateNumberMaxLength(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('RelateNumber cannot exceed 20 characters.');

        $this->query->setRelateNumber(str_repeat('A', 21));
    }

    public function testSetRelateNumber(): void
    {
        $this->query->setRelateNumber('ALLOWANCE2024001');
        $content = $this->getRawContent();

        $this->assertSame('ALLOWANCE2024001', $content['Data']['RelateNumber']);
    }

    // Full payload tests
    public function testFullPayloadWithAllowanceNumber(): void
    {
        $content = $this->query
            ->salesInvoice()
            ->setAllowanceNumber('AB123456780001')
            ->setAllowanceDate('2024-01-15')
            ->getContent();

        $this->assertArrayHasKey('Data', $content);
        $this->assertArrayHasKey('MerchantID', $content);
        $this->assertArrayHasKey('RqHeader', $content);
    }

    public function testFullPayloadWithRelateNumber(): void
    {
        $content = $this->query
            ->purchaseInvoice()
            ->setRelateNumber('ALLOWANCE2024001')
            ->getContent();

        $this->assertArrayHasKey('Data', $content);
    }

    /**
     * 取得未加密的原始內容。
     *
     * @return array
     */
    private function getRawContent(): array
    {
        $reflection = new ReflectionClass($this->query);
        $property = $reflection->getProperty('content');
        $property->setAccessible(true);

        return $property->getValue($this->query);
    }
}

