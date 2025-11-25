<?php

declare(strict_types=1);

use ecPay\eInvoiceB2B\DTO\InvoiceItemDto;
use ecPay\eInvoiceB2B\Operations\Issue;
use ecPay\eInvoiceB2B\Parameter\ExchangeMode;
use ecPay\eInvoiceB2B\Parameter\InvType;
use ecPay\eInvoiceB2B\Parameter\SpecialTaxType;
use ecPay\eInvoiceB2B\Parameter\TaxType;
use ecPay\eInvoiceB2B\Parameter\ZeroTaxRate;
use PHPUnit\Framework\TestCase;

class IssueTest extends TestCase
{
    private Issue $operation;

    protected function setUp(): void
    {
        $this->operation = new Issue(
            getenv('MERCHANT_ID') ?: '2000132',
            getenv('HASH_KEY') ?: 'ejCk326UnaZWKisg',
            getenv('HASH_IV') ?: 'q9jcZX8Ib9LM8wYk'
        );
    }

    public function testRequestPath(): void
    {
        $this->assertEquals('/B2BInvoice/Issue', $this->operation->getRequestPath());
    }

    // RelateNumber tests
    public function testRelateNumberRequired(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('RelateNumber cannot be empty.');

        $this->operation
            ->setInvoiceDate('2024-01-01')
            ->setBuyerIdentifier('12345678')
            ->setBuyerName('Test Company')
            ->setTotalAmount(1050)
            ->addItem(new InvoiceItemDto('Test', 1, '個', 1000, 1000))
            ->getContent();
    }

    public function testRelateNumberMaxLength(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('RelateNumber cannot exceed 30 characters.');

        $this->operation->setRelateNumber(str_repeat('A', 31));
    }

    public function testRelateNumberNoSpecialChars(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('RelateNumber should not contain special characters.');

        $this->operation->setRelateNumber('TEST-001');
    }

    public function testSetRelateNumber(): void
    {
        $this->operation->setRelateNumber('TEST001');
        $content = $this->getRawContent();

        $this->assertSame('TEST001', $content['Data']['RelateNumber']);
    }

    // InvoiceDate tests
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

    // InvType tests
    public function testSetInvType(): void
    {
        $this->operation->setInvType(InvType::GENERAL);
        $content = $this->getRawContent();

        $this->assertSame('07', $content['Data']['InvType']);
    }

    public function testInvalidInvType(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvType must be 07 (一般稅額) or 08 (特種稅額).');

        $this->operation->setInvType('99');
    }

    public function testGeneralInvoice(): void
    {
        $this->operation->generalInvoice();
        $content = $this->getRawContent();

        $this->assertSame('07', $content['Data']['InvType']);
    }

    public function testSpecialInvoice(): void
    {
        $this->operation->specialInvoice();
        $content = $this->getRawContent();

        $this->assertSame('08', $content['Data']['InvType']);
    }

    // ExchangeMode tests
    public function testSetExchangeMode(): void
    {
        $this->operation->setExchangeMode(ExchangeMode::EXCHANGE);
        $content = $this->getRawContent();

        $this->assertSame('1', $content['Data']['ExchangeMode']);
    }

    public function testArchiveMode(): void
    {
        $this->operation->archiveMode();
        $content = $this->getRawContent();

        $this->assertSame('0', $content['Data']['ExchangeMode']);
    }

    public function testExchangeMode(): void
    {
        $this->operation->exchangeMode();
        $content = $this->getRawContent();

        $this->assertSame('1', $content['Data']['ExchangeMode']);
    }

    // BuyerIdentifier tests
    public function testBuyerIdentifierInvalid(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Buyer_Identifier must be 8 digits.');

        $this->operation->setBuyerIdentifier('1234567');
    }

    public function testSetBuyerIdentifier(): void
    {
        $this->operation->setBuyerIdentifier('12345678');
        $content = $this->getRawContent();

        $this->assertSame('12345678', $content['Data']['Buyer_Identifier']);
    }

    // BuyerName tests
    public function testBuyerNameEmpty(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Buyer_Name cannot be empty.');

        $this->operation->setBuyerName('');
    }

    public function testSetBuyerName(): void
    {
        $this->operation->setBuyerName('測試公司');
        $content = $this->getRawContent();

        $this->assertSame('測試公司', $content['Data']['Buyer_Name']);
    }

    // TaxType tests
    public function testSetTaxType(): void
    {
        $this->operation->setTaxType(TaxType::TAXABLE);
        $content = $this->getRawContent();

        $this->assertSame('1', $content['Data']['TaxType']);
    }

    public function testTaxable(): void
    {
        $this->operation->taxable();
        $content = $this->getRawContent();

        $this->assertSame('1', $content['Data']['TaxType']);
    }

    public function testZeroTax(): void
    {
        $this->operation->zeroTax(ZeroTaxRate::NON_CUSTOMS_EXPORT);
        $content = $this->getRawContent();

        $this->assertSame('2', $content['Data']['TaxType']);
        $this->assertSame('1', $content['Data']['ZeroTaxRateReason']);
    }

    public function testTaxFree(): void
    {
        $this->operation->taxFree();
        $content = $this->getRawContent();

        $this->assertSame('3', $content['Data']['TaxType']);
    }

    public function testSpecialTax(): void
    {
        $this->operation->specialTax(SpecialTaxType::GOLF_10);
        $content = $this->getRawContent();

        $this->assertSame('4', $content['Data']['TaxType']);
        $this->assertSame('2', $content['Data']['SpecialTaxType']);
    }

    // Amount tests
    public function testSetSalesAmount(): void
    {
        $this->operation->setSalesAmount(1000);
        $content = $this->getRawContent();

        $this->assertSame(1000, $content['Data']['SalesAmount']);
    }

    public function testSalesAmountNegative(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('SalesAmount cannot be negative.');

        $this->operation->setSalesAmount(-100);
    }

    public function testSetAmounts(): void
    {
        $this->operation->setAmounts(1000, 50, 1050);
        $content = $this->getRawContent();

        $this->assertSame(1000, $content['Data']['SalesAmount']);
        $this->assertSame(50, $content['Data']['TaxAmount']);
        $this->assertSame(1050, $content['Data']['TotalAmount']);
    }

    // Items tests
    public function testAddItem(): void
    {
        $item = new InvoiceItemDto('商品A', 2, '個', 500, 1000, 50);
        $this->operation->addItem($item);

        $payload = $this->getPayloadWithItems();

        $this->assertCount(1, $payload['Data']['Items']);
        $this->assertSame('商品A', $payload['Data']['Items'][0]['ItemName']);
    }

    public function testItemsRequired(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('At least one item is required.');

        $this->operation
            ->setRelateNumber('TEST001')
            ->setInvoiceDate('2024-01-01')
            ->setBuyerIdentifier('12345678')
            ->setBuyerName('Test Company')
            ->setTotalAmount(1050)
            ->getContent();
    }

    // Validation tests
    public function testTaxTypeValidationForGeneralInvoice(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('For InvType 07, TaxType must be 1, 2, or 3.');

        $this->operation
            ->setRelateNumber('TEST001')
            ->setInvoiceDate('2024-01-01')
            ->generalInvoice()
            ->setTaxType(TaxType::SPECIAL_TAX)
            ->setBuyerIdentifier('12345678')
            ->setBuyerName('Test Company')
            ->setTotalAmount(1050)
            ->addItem(new InvoiceItemDto('Test', 1, '個', 1000, 1000))
            ->getContent();
    }

    public function testZeroTaxRateReasonRequired(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('ZeroTaxRateReason is required when TaxType is 2 (零稅率).');

        $this->operation
            ->setRelateNumber('TEST001')
            ->setInvoiceDate('2024-01-01')
            ->setBuyerIdentifier('12345678')
            ->setBuyerName('Test Company')
            ->setTaxType(TaxType::ZERO_TAX)
            ->setTotalAmount(1050)
            ->addItem(new InvoiceItemDto('Test', 1, '個', 1000, 1000))
            ->getContent();
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

    /**
     * 取得包含 Items 的 payload。
     */
    private function getPayloadWithItems(): array
    {
        $reflection = new ReflectionClass($this->operation);

        // 先處理 Items
        $method = $reflection->getMethod('getPayload');
        $method->setAccessible(true);

        // 由於 getPayload 會驗證，我們直接讀取 content
        $contentProperty = $reflection->getProperty('content');
        $contentProperty->setAccessible(true);

        $itemsProperty = $reflection->getProperty('items');
        $itemsProperty->setAccessible(true);

        $content = $contentProperty->getValue($this->operation);
        $items = $itemsProperty->getValue($this->operation);
        $content['Data']['Items'] = $items->toArray();

        return $content;
    }
}

