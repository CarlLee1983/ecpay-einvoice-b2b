<?php

declare(strict_types=1);

use CarlLee\EcPayB2B\Parameter\B2BInvoiceCategory;
use CarlLee\EcPayB2B\Queries\GetIssue;
use PHPUnit\Framework\TestCase;

class GetIssueTest extends TestCase
{
    private GetIssue $query;

    protected function setUp(): void
    {
        $this->query = new GetIssue(
            getenv('MERCHANT_ID') ?: '2000132',
            getenv('HASH_KEY') ?: 'ejCk326UnaZWKisg',
            getenv('HASH_IV') ?: 'q9jcZX8Ib9LM8wYk'
        );
    }

    public function testRequestPath(): void
    {
        $this->assertEquals('/B2BInvoice/GetIssue', $this->query->getRequestPath());
    }

    // InvoiceCategory tests
    public function testInvoiceCategoryRequired(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceCategory cannot be empty.');

        $this->query
            ->setInvoiceNumber('AB12345678')
            ->setInvoiceDate('2024-01-01')
            ->getContent();
    }

    public function testInvalidInvoiceCategory(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceCategory must be 0 (銷項發票) or 1 (進項發票).');

        $this->query->setInvoiceCategory(2);
    }

    public function testSetInvoiceCategorySales(): void
    {
        $this->query->setInvoiceCategory(B2BInvoiceCategory::SALES);
        $content = $this->getRawContent();

        $this->assertSame(0, $content['Data']['InvoiceCategory']);
    }

    public function testSetInvoiceCategoryPurchase(): void
    {
        $this->query->setInvoiceCategory(B2BInvoiceCategory::PURCHASE);
        $content = $this->getRawContent();

        $this->assertSame(1, $content['Data']['InvoiceCategory']);
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

    // InvoiceNumber tests
    public function testInvoiceNumberRequired(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceNumber cannot be empty.');

        $this->query
            ->salesInvoice()
            ->setInvoiceDate('2024-01-01')
            ->getContent();
    }

    public function testInvoiceNumberFormat(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceNumber must be 2 letters followed by 8 digits');

        $this->query->setInvoiceNumber('1234567890');
    }

    public function testInvoiceNumberTooShort(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceNumber must be 2 letters followed by 8 digits');

        $this->query->setInvoiceNumber('AB1234567');
    }

    public function testSetInvoiceNumber(): void
    {
        $this->query->setInvoiceNumber('AB12345678');
        $content = $this->getRawContent();

        $this->assertSame('AB12345678', $content['Data']['InvoiceNumber']);
    }

    public function testInvoiceNumberUppercase(): void
    {
        $this->query->setInvoiceNumber('ab12345678');
        $content = $this->getRawContent();

        $this->assertSame('AB12345678', $content['Data']['InvoiceNumber']);
    }

    // InvoiceDate tests
    public function testInvoiceDateRequired(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceDate cannot be empty.');

        $this->query
            ->salesInvoice()
            ->setInvoiceNumber('AB12345678')
            ->getContent();
    }

    public function testInvoiceDateInvalidFormat(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceDate must be in yyyy-mm-dd format.');

        $this->query->setInvoiceDate('2024/01/01');
    }

    public function testInvoiceDateInvalidDate(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceDate must be in yyyy-mm-dd format.');

        $this->query->setInvoiceDate('2024-13-01');
    }

    public function testSetInvoiceDate(): void
    {
        $this->query->setInvoiceDate('2024-01-15');
        $content = $this->getRawContent();

        $this->assertSame('2024-01-15', $content['Data']['InvoiceDate']);
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
        $this->query->setRelateNumber('ORDER2024001');
        $content = $this->getRawContent();

        $this->assertSame('ORDER2024001', $content['Data']['RelateNumber']);
    }

    // Full payload test
    public function testFullPayload(): void
    {
        $content = $this->query
            ->salesInvoice()
            ->setInvoiceNumber('AB12345678')
            ->setInvoiceDate('2024-01-15')
            ->setRelateNumber('ORDER001')
            ->getContent();

        $this->assertArrayHasKey('Data', $content);
        $this->assertArrayHasKey('MerchantID', $content);
        $this->assertArrayHasKey('RqHeader', $content);
    }

    public function testB2BInvoiceCategoryConstants(): void
    {
        $this->assertSame(0, B2BInvoiceCategory::SALES);
        $this->assertSame(1, B2BInvoiceCategory::PURCHASE);
        $this->assertTrue(B2BInvoiceCategory::isValid(0));
        $this->assertTrue(B2BInvoiceCategory::isValid(1));
        $this->assertFalse(B2BInvoiceCategory::isValid(2));
        $this->assertSame('銷項發票', B2BInvoiceCategory::getName(0));
        $this->assertSame('進項發票', B2BInvoiceCategory::getName(1));
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
