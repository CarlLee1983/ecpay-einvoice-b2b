<?php

declare(strict_types=1);

use CarlLee\EcPayB2B\Notifications\Notify;
use CarlLee\EcPayB2B\Parameter\InvoiceTag;
use CarlLee\EcPayB2B\Parameter\NotifyTarget;
use PHPUnit\Framework\TestCase;

class NotifyTest extends TestCase
{
    private Notify $operation;

    protected function setUp(): void
    {
        $this->operation = new Notify(
            getenv('MERCHANT_ID') ?: '2000132',
            getenv('HASH_KEY') ?: 'ejCk326UnaZWKisg',
            getenv('HASH_IV') ?: 'q9jcZX8Ib9LM8wYk'
        );
    }

    public function testRequestPath(): void
    {
        $this->assertEquals('/B2BInvoice/Notify', $this->operation->getRequestPath());
    }

    // InvoiceDate tests
    public function testInvoiceDateRequired(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceDate cannot be empty.');

        $this->operation
            ->setInvoiceNumber('AB12345678')
            ->setNotifyMail('test@example.com')
            ->setInvoiceTag(InvoiceTag::ISSUE)
            ->getContent();
    }

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

    // InvoiceNumber tests
    public function testInvoiceNumberRequired(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceNumber cannot be empty.');

        $this->operation
            ->setInvoiceDate('2024-01-01')
            ->setNotifyMail('test@example.com')
            ->setInvoiceTag(InvoiceTag::ISSUE)
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

    public function testInvoiceNumberUppercase(): void
    {
        $this->operation->setInvoiceNumber('ab12345678');
        $content = $this->getRawContent();

        $this->assertSame('AB12345678', $content['Data']['InvoiceNumber']);
    }

    // AllowanceNo tests
    public function testAllowanceNoLength(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('AllowanceNo must be exactly 16 characters.');

        $this->operation->setAllowanceNo('1234567890');
    }

    public function testSetAllowanceNo(): void
    {
        $this->operation->setAllowanceNo('AA123456780001AB');
        $content = $this->getRawContent();

        $this->assertSame('AA123456780001AB', $content['Data']['AllowanceNo']);
    }

    // NotifyMail tests
    public function testNotifyMailRequired(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('NotifyMail cannot be empty.');

        $this->operation
            ->setInvoiceDate('2024-01-01')
            ->setInvoiceNumber('AB12345678')
            ->setInvoiceTag(InvoiceTag::ISSUE)
            ->getContent();
    }

    public function testNotifyMailEmpty(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('NotifyMail cannot be empty.');

        $this->operation->setNotifyMail('');
    }

    public function testNotifyMailInvalidFormat(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('NotifyMail contains invalid email format');

        $this->operation->setNotifyMail('invalid-email');
    }

    public function testSetNotifyMail(): void
    {
        $this->operation->setNotifyMail('test@example.com');
        $content = $this->getRawContent();

        $this->assertSame('test@example.com', $content['Data']['NotifyMail']);
    }

    public function testSetNotifyMails(): void
    {
        $this->operation->setNotifyMails(['test1@example.com', 'test2@example.com']);
        $content = $this->getRawContent();

        $this->assertSame('test1@example.com;test2@example.com', $content['Data']['NotifyMail']);
    }

    // InvoiceTag tests
    public function testInvoiceTagRequired(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceTag cannot be empty.');

        $this->operation
            ->setInvoiceDate('2024-01-01')
            ->setInvoiceNumber('AB12345678')
            ->setNotifyMail('test@example.com')
            ->getContent();
    }

    public function testInvoiceTagInvalid(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('InvoiceTag must be between 1 and 10.');

        $this->operation->setInvoiceTag('99');
    }

    public function testSetInvoiceTag(): void
    {
        $this->operation->setInvoiceTag(InvoiceTag::ISSUE);
        $content = $this->getRawContent();

        $this->assertSame('1', $content['Data']['InvoiceTag']);
    }

    public function testIssueNotify(): void
    {
        $this->operation->issueNotify();
        $content = $this->getRawContent();

        $this->assertSame('1', $content['Data']['InvoiceTag']);
    }

    public function testInvalidNotify(): void
    {
        $this->operation->invalidNotify();
        $content = $this->getRawContent();

        $this->assertSame('2', $content['Data']['InvoiceTag']);
    }

    public function testRejectNotify(): void
    {
        $this->operation->rejectNotify();
        $content = $this->getRawContent();

        $this->assertSame('3', $content['Data']['InvoiceTag']);
    }

    public function testAllowanceNotify(): void
    {
        $this->operation->allowanceNotify('AA123456780001AB');
        $content = $this->getRawContent();

        $this->assertSame('4', $content['Data']['InvoiceTag']);
        $this->assertSame('AA123456780001AB', $content['Data']['AllowanceNo']);
    }

    public function testAllowanceInvalidNotify(): void
    {
        $this->operation->allowanceInvalidNotify('AA123456780001AB');
        $content = $this->getRawContent();

        $this->assertSame('5', $content['Data']['InvoiceTag']);
        $this->assertSame('AA123456780001AB', $content['Data']['AllowanceNo']);
    }

    // Notified tests
    public function testSetNotified(): void
    {
        $this->operation->setNotified(NotifyTarget::ALL);
        $content = $this->getRawContent();

        $this->assertSame('A', $content['Data']['Notified']);
    }

    public function testNotifiedInvalid(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Notified must be C (客戶), M (特店), or A (皆發送).');

        $this->operation->setNotified('X');
    }

    public function testNotifyCustomer(): void
    {
        $this->operation->notifyCustomer();
        $content = $this->getRawContent();

        $this->assertSame('C', $content['Data']['Notified']);
    }

    public function testNotifyMerchant(): void
    {
        $this->operation->notifyMerchant();
        $content = $this->getRawContent();

        $this->assertSame('M', $content['Data']['Notified']);
    }

    public function testNotifyAll(): void
    {
        $this->operation->notifyAll();
        $content = $this->getRawContent();

        $this->assertSame('A', $content['Data']['Notified']);
    }

    // Full payload test
    public function testFullPayload(): void
    {
        $content = $this->operation
            ->setInvoiceDate('2024-01-15')
            ->setInvoiceNumber('AB12345678')
            ->setNotifyMail('test@example.com')
            ->issueNotify()
            ->notifyAll()
            ->getContent();

        $this->assertArrayHasKey('Data', $content);
        $this->assertArrayHasKey('MerchantID', $content);
    }

    // Parameter class tests
    public function testInvoiceTagConstants(): void
    {
        $this->assertSame('1', InvoiceTag::ISSUE);
        $this->assertSame('2', InvoiceTag::INVALID);
        $this->assertSame('3', InvoiceTag::REJECT);
        $this->assertSame('4', InvoiceTag::ALLOWANCE);
        $this->assertSame('5', InvoiceTag::ALLOWANCE_INVALID);
        $this->assertSame('6', InvoiceTag::ISSUE_CONFIRM);
        $this->assertSame('7', InvoiceTag::INVALID_CONFIRM);
        $this->assertSame('8', InvoiceTag::REJECT_CONFIRM);
        $this->assertSame('9', InvoiceTag::ALLOWANCE_CONFIRM);
        $this->assertSame('10', InvoiceTag::ALLOWANCE_INVALID_CONFIRM);

        $this->assertTrue(InvoiceTag::isValid('1'));
        $this->assertTrue(InvoiceTag::isValid('10'));
        $this->assertFalse(InvoiceTag::isValid('11'));

        $this->assertSame('發票開立', InvoiceTag::getName('1'));
        $this->assertTrue(InvoiceTag::isAllowanceTag('4'));
        $this->assertFalse(InvoiceTag::isAllowanceTag('1'));
    }

    public function testNotifyTargetConstants(): void
    {
        $this->assertSame('C', NotifyTarget::CUSTOMER);
        $this->assertSame('M', NotifyTarget::MERCHANT);
        $this->assertSame('A', NotifyTarget::ALL);

        $this->assertTrue(NotifyTarget::isValid('C'));
        $this->assertTrue(NotifyTarget::isValid('M'));
        $this->assertTrue(NotifyTarget::isValid('A'));
        $this->assertFalse(NotifyTarget::isValid('X'));

        $this->assertSame('客戶', NotifyTarget::getName('C'));
        $this->assertSame('合作特店', NotifyTarget::getName('M'));
        $this->assertSame('皆發送', NotifyTarget::getName('A'));
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
}
