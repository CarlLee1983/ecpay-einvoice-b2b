<?php

declare(strict_types=1);

use ecPay\eInvoiceB2B\Parameter\B2BInvoiceCategory;
use ecPay\eInvoiceB2B\Queries\GetIssueConfirm;
use PHPUnit\Framework\TestCase;

class GetIssueConfirmTest extends TestCase
{
    private GetIssueConfirm $query;

    protected function setUp(): void
    {
        $this->query = new GetIssueConfirm(
            getenv('MERCHANT_ID') ?: '2000132',
            getenv('HASH_KEY') ?: 'ejCk326UnaZWKisg',
            getenv('HASH_IV') ?: 'q9jcZX8Ib9LM8wYk'
        );
    }

    public function testRequestPath(): void
    {
        $this->assertEquals('/B2BInvoice/GetIssueConfirm', $this->query->getRequestPath());
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

    // InvoiceNumber / RelateNumber required tests
    public function testEitherInvoiceNumberOrRelateNumberRequired(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Either InvoiceNumber or RelateNumber must be provided.');

        $this->query
            ->salesInvoice()
            ->getContent();
    }

    public function testInvoiceDateRequiredWhenInvoiceNumberProvided(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceDate is required when InvoiceNumber is provided.');

        $this->query
            ->salesInvoice()
            ->setInvoiceNumber('AB12345678')
            ->getContent();
    }

    public function testCanQueryByRelateNumberOnly(): void
    {
        $content = $this->query
            ->salesInvoice()
            ->setRelateNumber('ORDER2024001')
            ->getContent();

        $this->assertArrayHasKey('Data', $content);
    }

    // InvoiceNumber tests
    public function testInvoiceNumberFormat(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceNumber must be 2 letters followed by 8 digits');

        $this->query->setInvoiceNumber('1234567890');
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
    public function testInvoiceDateInvalidFormat(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceDate must be in yyyy-mm-dd format.');

        $this->query->setInvoiceDate('2024/01/01');
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

    // Identifier tests
    public function testSellerIdentifierFormat(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Seller_Identifier must be exactly 8 digits.');

        $this->query->setSellerIdentifier('1234567');
    }

    public function testSetSellerIdentifier(): void
    {
        $this->query->setSellerIdentifier('12345678');
        $content = $this->getRawContent();

        $this->assertSame('12345678', $content['Data']['Seller_Identifier']);
    }

    public function testBuyerIdentifierFormat(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Buyer_Identifier must be exactly 8 digits.');

        $this->query->setBuyerIdentifier('1234567A');
    }

    public function testSetBuyerIdentifier(): void
    {
        $this->query->setBuyerIdentifier('87654321');
        $content = $this->getRawContent();

        $this->assertSame('87654321', $content['Data']['Buyer_Identifier']);
    }

    // Date range tests
    public function testSetInvoiceDateRange(): void
    {
        $this->query->setInvoiceDateRange('2024-01-01', '2024-01-31');
        $content = $this->getRawContent();

        $this->assertSame('2024-01-01', $content['Data']['InvoiceDateBegin']);
        $this->assertSame('2024-01-31', $content['Data']['InvoiceDateEnd']);
    }

    public function testInvoiceDateRangeInvalidFormat(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceDateBegin must be in yyyy-mm-dd format.');

        $this->query->setInvoiceDateRange('2024/01/01', '2024-01-31');
    }

    // Number range tests
    public function testSetInvoiceNumberRange(): void
    {
        $this->query->setInvoiceNumberRange('00000001', '00000100');
        $content = $this->getRawContent();

        $this->assertSame('00000001', $content['Data']['InvoiceNumberBegin']);
        $this->assertSame('00000100', $content['Data']['InvoiceNumberEnd']);
    }

    public function testInvoiceNumberRangeInvalidFormat(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceNumberBegin must be exactly 8 digits.');

        $this->query->setInvoiceNumberRange('1234567', '00000100');
    }

    // Status tests
    public function testSetIssueStatus(): void
    {
        $this->query->setIssueStatus('1');
        $content = $this->getRawContent();

        $this->assertSame('1', $content['Data']['Issue_Status']);
    }

    public function testInvalidIssueStatus(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Issue_Status must be 0 (退回) or 1 (開立).');

        $this->query->setIssueStatus('2');
    }

    public function testSetInvalidStatus(): void
    {
        $this->query->setInvalidStatus('0');
        $content = $this->getRawContent();

        $this->assertSame('0', $content['Data']['Invalid_Status']);
    }

    public function testInvalidInvalidStatus(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid_Status must be 0 (未作廢) or 1 (已作廢).');

        $this->query->setInvalidStatus('2');
    }

    public function testSetExchangeMode(): void
    {
        $this->query->setExchangeMode('1');
        $content = $this->getRawContent();

        $this->assertSame('1', $content['Data']['ExchangeMode']);
    }

    public function testInvalidExchangeMode(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('ExchangeMode must be 0 (存證) or 1 (交換).');

        $this->query->setExchangeMode('2');
    }

    public function testSetExchangeStatus(): void
    {
        $this->query->setExchangeStatus('1');
        $content = $this->getRawContent();

        $this->assertSame('1', $content['Data']['ExchangeStatus']);
    }

    public function testInvalidExchangeStatus(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('ExchangeStatus must be 0 or 1.');

        $this->query->setExchangeStatus('2');
    }

    public function testSetUploadStatus(): void
    {
        $this->query->setUploadStatus('1');
        $content = $this->getRawContent();

        $this->assertSame('1', $content['Data']['Upload_Status']);
    }

    public function testInvalidUploadStatus(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Upload_Status must be 0 (未上傳), 1 (已上傳), or 2 (上傳失敗).');

        $this->query->setUploadStatus('3');
    }

    // Full payload tests
    public function testFullPayloadWithInvoiceNumber(): void
    {
        $content = $this->query
            ->salesInvoice()
            ->setInvoiceNumber('AB12345678')
            ->setInvoiceDate('2024-01-15')
            ->setSellerIdentifier('12345678')
            ->setBuyerIdentifier('87654321')
            ->getContent();

        $this->assertArrayHasKey('Data', $content);
        $this->assertArrayHasKey('MerchantID', $content);
        $this->assertArrayHasKey('RqHeader', $content);
    }

    public function testFullPayloadWithRelateNumber(): void
    {
        $content = $this->query
            ->purchaseInvoice()
            ->setRelateNumber('ORDER2024001')
            ->setIssueStatus('1')
            ->setInvalidStatus('0')
            ->setExchangeMode('1')
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

