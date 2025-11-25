<?php

declare(strict_types=1);

use CarlLee\EcPayB2B\Parameter\InvoiceCategory;
use CarlLee\EcPayB2B\Parameter\InvoiceTerm;
use CarlLee\EcPayB2B\Parameter\InvType;
use CarlLee\EcPayB2B\Parameter\UseStatus;
use CarlLee\EcPayB2B\Queries\GetInvoiceWordSetting;
use PHPUnit\Framework\TestCase;

class GetInvoiceWordSettingTest extends TestCase
{
    private GetInvoiceWordSetting $query;

    protected function setUp(): void
    {
        $this->query = new GetInvoiceWordSetting(
            getenv('MERCHANT_ID') ?: '2000132',
            getenv('HASH_KEY') ?: 'ejCk326UnaZWKisg',
            getenv('HASH_IV') ?: 'q9jcZX8Ib9LM8wYk'
        );
    }

    public function testInvoiceYearRequired(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceYear cannot be empty.');

        $this->query->getContent();
    }

    public function testInvoiceYearConversion(): void
    {
        $year = (int) date('Y');
        $expected = str_pad((string) ($year - 1911), 3, '0', STR_PAD_LEFT);

        $this->query->setInvoiceYear($year);
        $content = $this->getRawContent();

        $this->assertSame($expected, $content['Data']['InvoiceYear']);
    }

    public function testInvoiceTermOutOfRange(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceTerm must be between 0 and 6.');

        $this->query->setInvoiceTerm(7);
    }

    public function testUseStatusOutOfRange(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('UseStatus must be between 0 and 6.');

        $this->query->setUseStatus(-1);
    }

    public function testInvTypeValidation(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvType only supports 07');

        $this->query->setInvType('09');
    }

    public function testInvoiceCategoryMustBeB2B(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceCategory must be 2 (B2B).');

        $this->query->setInvoiceCategory(1);
    }

    public function testInvoiceHeaderValidation(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceHeader must contain exactly two letters.');

        $this->query->setInvoiceHeader('A1');
    }

    public function testRequestPath(): void
    {
        $this->assertEquals('/B2BInvoice/GetInvoiceWordSetting', $this->query->getRequestPath());
    }

    public function testDefaultInvoiceCategory(): void
    {
        $content = $this->getRawContent();
        $this->assertSame(InvoiceCategory::B2B, $content['Data']['InvoiceCategory']);
    }

    public function testFullPayload(): void
    {
        $content = $this->query
            ->setInvoiceYear((int) date('Y'))
            ->setInvoiceTerm(InvoiceTerm::ALL)
            ->setUseStatus(UseStatus::ALL)
            ->setInvType(InvType::GENERAL)
            ->setInvoiceHeader('AB')
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
        $reflection = new ReflectionClass($this->query);
        $property = $reflection->getProperty('content');
        $property->setAccessible(true);

        return $property->getValue($this->query);
    }
}
