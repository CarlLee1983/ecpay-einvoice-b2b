<?php

declare(strict_types=1);

use CarlLee\EcPayB2B\Operations\AddInvoiceWordSetting;
use CarlLee\EcPayB2B\Parameter\InvoiceCategory;
use CarlLee\EcPayB2B\Parameter\InvoiceTerm;
use CarlLee\EcPayB2B\Parameter\InvType;
use PHPUnit\Framework\TestCase;

class AddInvoiceWordSettingTest extends TestCase
{
    private AddInvoiceWordSetting $operation;

    protected function setUp(): void
    {
        $this->operation = new AddInvoiceWordSetting(
            getenv('MERCHANT_ID') ?: '2000132',
            getenv('HASH_KEY') ?: 'ejCk326UnaZWKisg',
            getenv('HASH_IV') ?: 'q9jcZX8Ib9LM8wYk'
        );
    }

    public function testRequestPath(): void
    {
        $this->assertEquals('/B2BInvoice/AddInvoiceWordSetting', $this->operation->getRequestPath());
    }

    public function testInvoiceTermRequired(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceTerm cannot be empty.');

        $this->operation
            ->setInvoiceYear(date('Y'))
            ->setInvoiceHeader('TW')
            ->setInvoiceRange('10000000', '10000049')
            ->getContent();
    }

    public function testInvoiceTermCannotBeZero(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceTerm must be between 1 and 6.');

        $this->operation->setInvoiceTerm(0);
    }

    public function testInvoiceTermOutOfRange(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceTerm must be between 1 and 6.');

        $this->operation->setInvoiceTerm(7);
    }

    public function testSetInvoiceTerm(): void
    {
        $this->operation->setInvoiceTerm(InvoiceTerm::JAN_FEB);
        $content = $this->getRawContent();

        $this->assertSame(1, $content['Data']['InvoiceTerm']);
    }

    public function testInvoiceYearRequired(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceYear cannot be empty.');

        $this->operation
            ->setInvoiceTerm(InvoiceTerm::JAN_FEB)
            ->setInvoiceHeader('TW')
            ->setInvoiceRange('10000000', '10000049')
            ->getContent();
    }

    public function testInvoiceYearConversion(): void
    {
        $year = (int) date('Y');
        $expected = str_pad((string) ($year - 1911), 3, '0', STR_PAD_LEFT);

        $this->operation->setInvoiceYear($year);
        $content = $this->getRawContent();

        $this->assertSame($expected, $content['Data']['InvoiceYear']);
    }

    public function testInvoiceYearOnlyCurrentOrNext(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceYear can only be current or next year.');

        $lastYear = (int) date('Y') - 1;
        $this->operation->setInvoiceYear($lastYear);
    }

    public function testInvTypeValidation(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvType only supports 07');

        $this->operation->setInvType('09');
    }

    public function testSetInvType(): void
    {
        $this->operation->setInvType(InvType::SPECIAL);
        $content = $this->getRawContent();

        $this->assertSame('08', $content['Data']['InvType']);
    }

    public function testInvoiceCategoryMustBeB2B(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceCategory must be 2 (B2B).');

        $this->operation->setInvoiceCategory(1);
    }

    public function testInvoiceHeaderRequired(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceHeader cannot be empty.');

        $this->operation
            ->setInvoiceTerm(InvoiceTerm::JAN_FEB)
            ->setInvoiceYear(date('Y'))
            ->setInvoiceRange('10000000', '10000049')
            ->getContent();
    }

    public function testInvoiceHeaderValidation(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceHeader must contain exactly two letters.');

        $this->operation->setInvoiceHeader('A1');
    }

    public function testSetInvoiceHeader(): void
    {
        $this->operation->setInvoiceHeader('tw');
        $content = $this->getRawContent();

        $this->assertSame('TW', $content['Data']['InvoiceHeader']);
    }

    public function testInvoiceStartRequired(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceStart cannot be empty.');

        $this->operation
            ->setInvoiceTerm(InvoiceTerm::JAN_FEB)
            ->setInvoiceYear(date('Y'))
            ->setInvoiceHeader('TW')
            ->setInvoiceEnd('10000049')
            ->getContent();
    }

    public function testInvoiceStartMustBe8Digits(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceStart must be exactly 8 digits.');

        $this->operation->setInvoiceStart('1234567');
    }

    public function testInvoiceStartMustEndWith00Or50(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceStart must end with 00 or 50.');

        $this->operation->setInvoiceStart('10000001');
    }

    public function testInvoiceEndRequired(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceEnd cannot be empty.');

        $this->operation
            ->setInvoiceTerm(InvoiceTerm::JAN_FEB)
            ->setInvoiceYear(date('Y'))
            ->setInvoiceHeader('TW')
            ->setInvoiceStart('10000000')
            ->getContent();
    }

    public function testInvoiceEndMustBe8Digits(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceEnd must be exactly 8 digits.');

        $this->operation->setInvoiceEnd('1234567');
    }

    public function testInvoiceEndMustEndWith49Or99(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceEnd must end with 49 or 99.');

        $this->operation->setInvoiceEnd('10000048');
    }

    public function testInvoiceRangeMustBePaired(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invoice range must be 00-49 or 50-99 pair.');

        $this->operation
            ->setInvoiceTerm(InvoiceTerm::JAN_FEB)
            ->setInvoiceYear(date('Y'))
            ->setInvoiceHeader('TW')
            ->setInvoiceStart('10000000')
            ->setInvoiceEnd('10000099')
            ->getContent();
    }

    public function testInvoiceEndMustBeGreaterThanStart(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceEnd must be greater than InvoiceStart.');

        $this->operation
            ->setInvoiceTerm(InvoiceTerm::JAN_FEB)
            ->setInvoiceYear(date('Y'))
            ->setInvoiceHeader('TW')
            ->setInvoiceStart('10000050')
            ->setInvoiceEnd('10000049')
            ->getContent();
    }

    public function testSetInvoiceRange(): void
    {
        $this->operation->setInvoiceRange('10000000', '10000049');
        $content = $this->getRawContent();

        $this->assertSame('10000000', $content['Data']['InvoiceStart']);
        $this->assertSame('10000049', $content['Data']['InvoiceEnd']);
    }

    public function testDefaultInvoiceCategory(): void
    {
        $content = $this->getRawContent();

        $this->assertSame((string) InvoiceCategory::B2B, $content['Data']['InvoiceCategory']);
    }

    public function testDefaultInvType(): void
    {
        $content = $this->getRawContent();

        $this->assertSame(InvType::GENERAL, $content['Data']['InvType']);
    }

    public function testFullPayload(): void
    {
        $content = $this->operation
            ->setInvoiceYear(date('Y'))
            ->setInvoiceTerm(InvoiceTerm::JAN_FEB)
            ->setInvType(InvType::GENERAL)
            ->setInvoiceHeader('TW')
            ->setInvoiceRange('10000000', '10000049')
            ->getContent();

        $this->assertArrayHasKey('Data', $content);
        $this->assertArrayHasKey('MerchantID', $content);
        $this->assertArrayHasKey('RqHeader', $content);
    }

    public function testValidRange5099(): void
    {
        $this->operation->setInvoiceRange('10000050', '10000099');
        $content = $this->getRawContent();

        $this->assertSame('10000050', $content['Data']['InvoiceStart']);
        $this->assertSame('10000099', $content['Data']['InvoiceEnd']);
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

