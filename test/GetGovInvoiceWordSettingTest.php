<?php

declare(strict_types=1);

use ecPay\eInvoiceB2B\Queries\GetGovInvoiceWordSetting;
use PHPUnit\Framework\TestCase;

class GetGovInvoiceWordSettingTest extends TestCase
{
    private GetGovInvoiceWordSetting $query;

    protected function setUp(): void
    {
        $this->query = new GetGovInvoiceWordSetting(
            getenv('MERCHANT_ID') ?: '2000132',
            getenv('HASH_KEY') ?: 'ejCk326UnaZWKisg',
            getenv('HASH_IV') ?: 'q9jcZX8Ib9LM8wYk'
        );
    }

    public function testRequestPath(): void
    {
        $this->assertEquals('/B2BInvoice/GetGovInvoiceWordSetting', $this->query->getRequestPath());
    }

    public function testInvoiceYearRequired(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceYear cannot be empty.');

        $this->query->getContent();
    }

    public function testInvoiceYearCannotBeEmpty(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceYear cannot be empty.');

        $this->query->setInvoiceYear('');
    }

    public function testInvoiceYearMustBeNumeric(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceYear must be numeric.');

        $this->query->setInvoiceYear('abc');
    }

    public function testInvoiceYearConversionFromGregorian(): void
    {
        $year = (int) date('Y');
        $expected = str_pad((string) ($year - 1911), 3, '0', STR_PAD_LEFT);

        $this->query->setInvoiceYear($year);
        $content = $this->getRawContent();

        $this->assertSame($expected, $content['Data']['InvoiceYear']);
    }

    public function testInvoiceYearConversionFromROC(): void
    {
        $rocYear = (int) date('Y') - 1911;
        $expected = str_pad((string) $rocYear, 3, '0', STR_PAD_LEFT);

        $this->query->setInvoiceYear($rocYear);
        $content = $this->getRawContent();

        $this->assertSame($expected, $content['Data']['InvoiceYear']);
    }

    public function testInvoiceYearCanQueryLastYear(): void
    {
        $lastYear = (int) date('Y') - 1;
        $expected = str_pad((string) ($lastYear - 1911), 3, '0', STR_PAD_LEFT);

        $this->query->setInvoiceYear($lastYear);
        $content = $this->getRawContent();

        $this->assertSame($expected, $content['Data']['InvoiceYear']);
    }

    public function testInvoiceYearCanQueryNextYear(): void
    {
        $nextYear = (int) date('Y') + 1;
        $expected = str_pad((string) ($nextYear - 1911), 3, '0', STR_PAD_LEFT);

        $this->query->setInvoiceYear($nextYear);
        $content = $this->getRawContent();

        $this->assertSame($expected, $content['Data']['InvoiceYear']);
    }

    public function testInvoiceYearCannotQueryTwoYearsAgo(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceYear can only target last, current, or next year.');

        $twoYearsAgo = (int) date('Y') - 2;
        $this->query->setInvoiceYear($twoYearsAgo);
    }

    public function testInvoiceYearCannotQueryTwoYearsLater(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceYear can only target last, current, or next year.');

        $twoYearsLater = (int) date('Y') + 2;
        $this->query->setInvoiceYear($twoYearsLater);
    }

    public function testGregorianYearMustBeAfter1911(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Gregorian year must be greater than 1911.');

        $this->query->setInvoiceYear(1911);
    }

    public function testFullPayload(): void
    {
        $content = $this->query
            ->setInvoiceYear(date('Y'))
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

