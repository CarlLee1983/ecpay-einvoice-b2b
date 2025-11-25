<?php

declare(strict_types=1);

use CarlLee\EcPayB2B\Operations\UpdateInvoiceWordStatus;
use CarlLee\EcPayB2B\Parameter\InvoiceWordStatus;
use PHPUnit\Framework\TestCase;

class UpdateInvoiceWordStatusTest extends TestCase
{
    private UpdateInvoiceWordStatus $operation;

    protected function setUp(): void
    {
        $this->operation = new UpdateInvoiceWordStatus(
            getenv('MERCHANT_ID') ?: '2000132',
            getenv('HASH_KEY') ?: 'ejCk326UnaZWKisg',
            getenv('HASH_IV') ?: 'q9jcZX8Ib9LM8wYk'
        );
    }

    public function testRequestPath(): void
    {
        $this->assertEquals('/B2BInvoice/UpdateInvoiceWordStatus', $this->operation->getRequestPath());
    }

    public function testTrackIDRequired(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('TrackID cannot be empty.');

        $this->operation->getContent();
    }

    public function testTrackIDCannotBeEmpty(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('TrackID cannot be empty.');

        $this->operation->setTrackID('   ');
    }

    public function testTrackIDMaxLength(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('TrackID cannot exceed 10 characters.');

        $this->operation->setTrackID('12345678901');
    }

    public function testSetTrackID(): void
    {
        $this->operation->setTrackID('1234567890');
        $content = $this->getRawContent();

        $this->assertSame('1234567890', $content['Data']['TrackID']);
    }

    public function testInvalidInvoiceStatus(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceStatus must be 0 (停用), 1 (暫停), or 2 (啟用).');

        $this->operation->setInvoiceStatus(3);
    }

    public function testSetInvoiceStatusDisabled(): void
    {
        $this->operation->setInvoiceStatus(InvoiceWordStatus::DISABLED);
        $content = $this->getRawContent();

        $this->assertSame(0, $content['Data']['InvoiceStatus']);
    }

    public function testSetInvoiceStatusSuspended(): void
    {
        $this->operation->setInvoiceStatus(InvoiceWordStatus::SUSPENDED);
        $content = $this->getRawContent();

        $this->assertSame(1, $content['Data']['InvoiceStatus']);
    }

    public function testSetInvoiceStatusEnabled(): void
    {
        $this->operation->setInvoiceStatus(InvoiceWordStatus::ENABLED);
        $content = $this->getRawContent();

        $this->assertSame(2, $content['Data']['InvoiceStatus']);
    }

    public function testEnableMethod(): void
    {
        $this->operation->enable();
        $content = $this->getRawContent();

        $this->assertSame(InvoiceWordStatus::ENABLED, $content['Data']['InvoiceStatus']);
    }

    public function testSuspendMethod(): void
    {
        $this->operation->suspend();
        $content = $this->getRawContent();

        $this->assertSame(InvoiceWordStatus::SUSPENDED, $content['Data']['InvoiceStatus']);
    }

    public function testDisableMethod(): void
    {
        $this->operation->disable();
        $content = $this->getRawContent();

        $this->assertSame(InvoiceWordStatus::DISABLED, $content['Data']['InvoiceStatus']);
    }

    public function testDefaultInvoiceStatus(): void
    {
        $content = $this->getRawContent();

        $this->assertSame(InvoiceWordStatus::ENABLED, $content['Data']['InvoiceStatus']);
    }

    public function testFullPayload(): void
    {
        $content = $this->operation
            ->setTrackID('1234567890')
            ->setInvoiceStatus(InvoiceWordStatus::ENABLED)
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

