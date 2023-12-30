<?php

declare(strict_types=1);

namespace Skd\Result\Tests\Unit\Error;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Skd\Result\Error\Error;
use Skd\Result\Error\Notification;

/**
 * @internal
 *
 * @coversNothing
 */
#[CoversClass(Notification::class)]
class NotificationTest extends TestCase
{
    public function testCreatingEmptyNotification(): void
    {
        $notification = new Notification();

        $this->assertFalse($notification->hasErrors());
        $this->assertEmpty($notification->getErrors());
    }

    public function testCreatingNotificationWithError(): void
    {
        $error = new Error('code', 'message');
        $notification = new Notification($error);

        $this->assertTrue($notification->hasErrors());
        $this->assertTrue($notification->hasError($error));
        $this->assertCount(1, $notification->getErrors());
    }

    public function testAddingErrors(): void
    {
        $error = new Error('code', 'message');
        $anotherError = new Error('anotherCode', 'another message');

        $notification = new Notification($error);
        $notification->addError($anotherError);

        $this->assertTrue($notification->hasErrors());
        $this->assertTrue($notification->hasError($error));
        $this->assertTrue($notification->hasError($anotherError));
        $this->assertCount(2, $notification->getErrors());
    }

    public function testAddingErrorDuplicate(): void
    {
        $error = new Error('code', 'message');
        $notification = new Notification();

        $notification->addError($error);
        $notification->addError($error);

        $this->assertTrue($notification->hasErrors());
        $this->assertTrue($notification->hasError($error));
        $this->assertCount(1, $notification->getErrors());
    }

    public function testMerge(): void
    {
        $error = new Error('code', 'message');
        $anotherError = new Error('anotherCode', 'another message');

        $notification = new Notification($error);
        $anotherNotification = new Notification($anotherError);

        $notification->merge($anotherNotification);

        $this->assertTrue($notification->hasErrors());
        $this->assertTrue($anotherNotification->hasErrors());

        $this->assertCount(2, $notification->getErrors());
        $this->assertCount(1, $anotherNotification->getErrors());

        $this->assertTrue($notification->hasError($error));
        $this->assertTrue($notification->hasError($anotherError));

        $this->assertFalse($anotherNotification->hasError($error));
        $this->assertTrue($anotherNotification->hasError($anotherError));
    }

    public function testMergeWithDuplicates(): void
    {
        $error = new Error('code', 'message');
        $anotherError = new Error('anotherCode', 'another message');

        $notification = new Notification($error);
        $anotherNotification = new Notification(clone $error);
        $notification->addError($anotherError);
        $notification->addError($anotherError);

        $notification->merge($anotherNotification);

        $this->assertCount(2, $notification->getErrors());
        $this->assertTrue($notification->hasError($error));
        $this->assertTrue($notification->hasError($anotherError));
    }
}
