<?php

declare(strict_types=1);

use CarlLee\EcPayB2B\Operations\MaintainMerchantCustomerData;
use CarlLee\EcPayB2B\Parameter\CustomerType;
use CarlLee\EcPayB2B\Parameter\ExchangeMode;
use CarlLee\EcPayB2B\Parameter\MaintainAction;
use PHPUnit\Framework\TestCase;

class MaintainMerchantCustomerDataTest extends TestCase
{
    private MaintainMerchantCustomerData $operation;

    protected function setUp(): void
    {
        $this->operation = new MaintainMerchantCustomerData(
            getenv('MERCHANT_ID') ?: '2000132',
            getenv('HASH_KEY') ?: 'ejCk326UnaZWKisg',
            getenv('HASH_IV') ?: 'q9jcZX8Ib9LM8wYk'
        );
    }

    public function testRequestPath(): void
    {
        $this->assertEquals('/B2BInvoice/MaintainMerchantCustomerData', $this->operation->getRequestPath());
    }

    // Action tests
    public function testActionRequired(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Action cannot be empty.');

        $this->operation
            ->setIdentifier('12345678')
            ->setType(CustomerType::BUYER)
            ->setEmailAddress('test@example.com')
            ->getContent();
    }

    public function testInvalidAction(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Action must be Add, Update, or Delete.');

        $this->operation->setAction('Invalid');
    }

    public function testSetActionAdd(): void
    {
        $this->operation->setAction(MaintainAction::ADD);
        $content = $this->getRawContent();

        $this->assertSame('Add', $content['Data']['Action']);
    }

    public function testAddMethod(): void
    {
        $this->operation->add();
        $content = $this->getRawContent();

        $this->assertSame('Add', $content['Data']['Action']);
    }

    public function testUpdateMethod(): void
    {
        $this->operation->update();
        $content = $this->getRawContent();

        $this->assertSame('Update', $content['Data']['Action']);
    }

    public function testDeleteMethod(): void
    {
        $this->operation->delete();
        $content = $this->getRawContent();

        $this->assertSame('Delete', $content['Data']['Action']);
    }

    // Identifier tests
    public function testIdentifierRequired(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Identifier cannot be empty.');

        $this->operation
            ->add()
            ->setType(CustomerType::BUYER)
            ->setEmailAddress('test@example.com')
            ->setExchangeMode(ExchangeMode::ARCHIVE)
            ->getContent();
    }

    public function testIdentifierMustBe8Digits(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Identifier must be exactly 8 digits.');

        $this->operation->setIdentifier('1234567');
    }

    public function testIdentifierMustBeNumeric(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Identifier must be exactly 8 digits.');

        $this->operation->setIdentifier('1234567A');
    }

    public function testSetIdentifier(): void
    {
        $this->operation->setIdentifier('12345678');
        $content = $this->getRawContent();

        $this->assertSame('12345678', $content['Data']['Identifier']);
    }

    // Type tests
    public function testTypeRequired(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('type cannot be empty.');

        $this->operation
            ->add()
            ->setIdentifier('12345678')
            ->setEmailAddress('test@example.com')
            ->setExchangeMode(ExchangeMode::ARCHIVE)
            ->getContent();
    }

    public function testInvalidType(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('type must be 1 (買方), 2 (賣方), or 3 (買賣方).');

        $this->operation->setType('4');
    }

    public function testSetTypeBuyer(): void
    {
        $this->operation->setType(CustomerType::BUYER);
        $content = $this->getRawContent();

        $this->assertSame('1', $content['Data']['type']);
    }

    public function testAsBuyerMethod(): void
    {
        $this->operation->asBuyer();
        $content = $this->getRawContent();

        $this->assertSame('1', $content['Data']['type']);
    }

    public function testAsSellerMethod(): void
    {
        $this->operation->asSeller();
        $content = $this->getRawContent();

        $this->assertSame('2', $content['Data']['type']);
    }

    public function testAsBothMethod(): void
    {
        $this->operation->asBoth();
        $content = $this->getRawContent();

        $this->assertSame('3', $content['Data']['type']);
    }

    // ExchangeMode tests
    public function testExchangeModeRequiredWhenAdding(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('ExchangeMode cannot be empty when adding.');

        $this->operation
            ->add()
            ->setIdentifier('12345678')
            ->setType(CustomerType::BUYER)
            ->setEmailAddress('test@example.com')
            ->getContent();
    }

    public function testInvalidExchangeMode(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('ExchangeMode must be 0 (存證) or 1 (交換).');

        $this->operation->setExchangeMode('2');
    }

    public function testSetExchangeModeArchive(): void
    {
        $this->operation->setExchangeMode(ExchangeMode::ARCHIVE);
        $content = $this->getRawContent();

        $this->assertSame('0', $content['Data']['ExchangeMode']);
    }

    public function testArchiveModeMethod(): void
    {
        $this->operation->archiveMode();
        $content = $this->getRawContent();

        $this->assertSame('0', $content['Data']['ExchangeMode']);
    }

    public function testExchangeModeMethod(): void
    {
        $this->operation->exchangeMode();
        $content = $this->getRawContent();

        $this->assertSame('1', $content['Data']['ExchangeMode']);
    }

    // EmailAddress tests
    public function testEmailAddressRequired(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('EmailAddress cannot be empty.');

        $this->operation
            ->add()
            ->setIdentifier('12345678')
            ->setType(CustomerType::BUYER)
            ->setExchangeMode(ExchangeMode::ARCHIVE)
            ->getContent();
    }

    public function testSetEmailAddressString(): void
    {
        $this->operation->setEmailAddress('test@example.com');
        $content = $this->getRawContent();

        $this->assertSame('test@example.com', $content['Data']['EmailAddress']);
    }

    public function testSetEmailAddressArray(): void
    {
        $this->operation->setEmailAddress(['a@test.com', 'b@test.com', 'c@test.com']);
        $content = $this->getRawContent();

        $this->assertSame('a@test.com;b@test.com;c@test.com', $content['Data']['EmailAddress']);
    }

    public function testInvalidEmailFormat(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid email format:');

        $this->operation->setEmailAddress('invalid-email');
    }

    // CustomerNumber tests
    public function testCustomerNumberMaxLength(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('CustomerNumber cannot exceed 20 characters.');

        $this->operation->setCustomerNumber(str_repeat('A', 21));
    }

    public function testSetCustomerNumber(): void
    {
        $this->operation->setCustomerNumber('CUST001');
        $content = $this->getRawContent();

        $this->assertSame('CUST001', $content['Data']['CustomerNumber']);
    }

    // Other optional fields
    public function testSetCompanyName(): void
    {
        $this->operation->setCompanyName('測試公司');
        $content = $this->getRawContent();

        $this->assertSame('測試公司', $content['Data']['CompanyName']);
    }

    public function testSetAddress(): void
    {
        $this->operation->setAddress('台北市測試路1號');
        $content = $this->getRawContent();

        $this->assertSame('台北市測試路1號', $content['Data']['Address']);
    }

    public function testSetTelephoneNumber(): void
    {
        $this->operation->setTelephoneNumber('02-12345678');
        $content = $this->getRawContent();

        $this->assertSame('02-12345678', $content['Data']['TelephoneNumber']);
    }

    public function testSetTradingSlang(): void
    {
        $this->operation->setTradingSlang('ABC123');
        $content = $this->getRawContent();

        $this->assertSame('ABC123', $content['Data']['TradingSlang']);
    }

    public function testSalesNameMaxLength(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('SalesName cannot exceed 20 characters.');

        $this->operation->setSalesName(str_repeat('王', 21));
    }

    public function testSetSalesName(): void
    {
        $this->operation->setSalesName('王小明');
        $content = $this->getRawContent();

        $this->assertSame('王小明', $content['Data']['SalesName']);
    }

    public function testContactAddressMaxLength(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('ContactAddress cannot exceed 100 characters.');

        $this->operation->setContactAddress(str_repeat('A', 101));
    }

    public function testSetContactAddress(): void
    {
        $this->operation->setContactAddress('台北市聯絡地址');
        $content = $this->getRawContent();

        $this->assertSame('台北市聯絡地址', $content['Data']['ContactAddress']);
    }

    // Full payload test
    public function testFullPayload(): void
    {
        $content = $this->operation
            ->add()
            ->setIdentifier('12345678')
            ->asSeller()
            ->setCompanyName('測試公司')
            ->setAddress('台北市測試路1號')
            ->exchangeMode()
            ->setEmailAddress('test@example.com')
            ->getContent();

        $this->assertArrayHasKey('Data', $content);
        $this->assertArrayHasKey('MerchantID', $content);
        $this->assertArrayHasKey('RqHeader', $content);
    }

    /**
     * 取得未加密的原始內容。
     *
     * @return array
     */
    private function getRawContent(): array
    {
        $reflection = new ReflectionClass($this->operation);
        $property = $reflection->getProperty('content');
        $property->setAccessible(true);

        return $property->getValue($this->operation);
    }
}

