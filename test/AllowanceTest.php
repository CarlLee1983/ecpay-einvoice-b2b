<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Tests;

use Exception;
use ReflectionClass;
use CarlLee\EcPayB2B\DTO\AllowanceItemDto;
use CarlLee\EcPayB2B\Operations\Allowance;
use CarlLee\EcPayB2B\Parameter\TaxType;
use PHPUnit\Framework\TestCase;

class AllowanceTest extends TestCase
{
    private Allowance $operation;

    protected function setUp(): void
    {
        $this->operation = new Allowance(
            getenv('MERCHANT_ID') ?: '2000132',
            getenv('HASH_KEY') ?: 'ejCk326UnaZWKisg',
            getenv('HASH_IV') ?: 'q9jcZX8Ib9LM8wYk'
        );
    }

    public function testRequestPath(): void
    {
        $this->assertEquals('/B2BInvoice/Allowance', $this->operation->getRequestPath());
    }

    // RelateNumber tests
    public function testRelateNumberRequired(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('RelateNumber cannot be empty.');

        $this->operation
            ->setAllowanceDate('2024-01-01')
            ->setInvoiceNumber('AB12345678')
            ->setInvoiceDate('2024-01-01')
            ->setBuyerIdentifier('12345678')
            ->setBuyerName('Test Company')
            ->setAllowanceAmount(100)
            ->addItem(new AllowanceItemDto('Test', 1, '個', 100, 100))
            ->getContent();
    }

    public function testRelateNumberMaxLength(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('RelateNumber cannot exceed 30 characters.');

        $this->operation->setRelateNumber(str_repeat('A', 31));
    }

    public function testSetRelateNumber(): void
    {
        $this->operation->setRelateNumber('ALWN001');
        $content = $this->getRawContent();

        $this->assertSame('ALWN001', $content['Data']['RelateNumber']);
    }

    // AllowanceDate tests
    public function testAllowanceDateRequired(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('AllowanceDate cannot be empty.');

        $this->operation
            ->setRelateNumber('ALWN001')
            ->setInvoiceNumber('AB12345678')
            ->setInvoiceDate('2024-01-01')
            ->setBuyerIdentifier('12345678')
            ->setBuyerName('Test Company')
            ->setAllowanceAmount(100)
            ->addItem(new AllowanceItemDto('Test', 1, '個', 100, 100))
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

    // InvoiceNumber tests
    public function testInvoiceNumberRequired(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceNumber cannot be empty.');

        $this->operation
            ->setRelateNumber('ALWN001')
            ->setAllowanceDate('2024-01-01')
            ->setInvoiceDate('2024-01-01')
            ->setBuyerIdentifier('12345678')
            ->setBuyerName('Test Company')
            ->setAllowanceAmount(100)
            ->addItem(new AllowanceItemDto('Test', 1, '個', 100, 100))
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

    // AllowanceAmount tests
    public function testAllowanceAmountZero(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('AllowanceAmount must be greater than 0.');

        $this->operation->setAllowanceAmount(0);
    }

    public function testSetAllowanceAmount(): void
    {
        $this->operation->setAllowanceAmount(1000);
        $content = $this->getRawContent();

        $this->assertSame(1000, $content['Data']['AllowanceAmount']);
    }

    // Items tests
    public function testAddItem(): void
    {
        $item = new AllowanceItemDto('折讓商品', 1, '個', 100, 100, TaxType::TAXABLE, 5);
        $this->operation->addItem($item);

        $payload = $this->getPayloadWithItems();

        $this->assertCount(1, $payload['Data']['Items']);
        $this->assertSame('折讓商品', $payload['Data']['Items'][0]['ItemName']);
    }

    public function testItemsRequired(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('At least one item is required.');

        $this->operation
            ->setRelateNumber('ALWN001')
            ->setAllowanceDate('2024-01-01')
            ->setInvoiceNumber('AB12345678')
            ->setInvoiceDate('2024-01-01')
            ->setBuyerIdentifier('12345678')
            ->setBuyerName('Test Company')
            ->setAllowanceAmount(100)
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
