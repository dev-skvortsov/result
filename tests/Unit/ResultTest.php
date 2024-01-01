<?php

declare(strict_types=1);

namespace Skd\Result\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Skd\Result\Error\Error;
use Skd\Result\Error\Notification;
use Skd\Result\Exception\ResultException;
use Skd\Result\Ok;
use Skd\Result\Result;

/**
 * @internal
 */
#[CoversClass(Result::class)]
class ResultTest extends TestCase
{
    public function testOkay(): void
    {
        $value = 'string-value';

        $result = Result::ok($value);

        $this->assertTrue($result->isOk());
        $this->assertFalse($result->isError());
        $this->assertEquals($value, $result->getValue());
    }

    public function testEmptyOkay(): void
    {
        $result = Result::ok();

        $this->assertTrue($result->isOk());
        $this->assertInstanceOf(Ok::class, $result->getValue());
    }

    public function testNullValue(): void
    {
        $this->expectExceptionMessage(ResultException::VALUE_CANNOT_BE_NULL);

        Result::ok(null);
    }

    public function testError(): void
    {
        $error = new Error('code', 'message');
        $notification = new Notification($error);

        $result = Result::error($notification);

        $this->assertFalse($result->isOk());
        $this->assertTrue($result->isError());
    }

    public function testGettingErrorOnOkayResult(): void
    {
        $this->expectExceptionMessage(ResultException::RESULT_IN_OK_STATE);

        $result = Result::ok();

        $result->getErrors();
    }

    public function testGettingValueOnErrorResult(): void
    {
        $error = new Error('code', 'message');
        $notification = new Notification($error);

        $this->expectExceptionMessage(ResultException::RESULT_IN_ERROR_STATE);

        $result = Result::error($notification);

        $result->getValue();
    }

    public function testCreatingErrorWithEmptyNotification(): void
    {
        $this->expectExceptionMessage(ResultException::ERRORS_LIST_IS_EMPTY);

        Result::error(new Notification());
    }

    public function testNotificationImmutability(): void
    {
        $error = new Error('code', 'message');
        $anotherError = new Error('anotherCode', 'another message');
        $notification = new Notification($error);

        $result = Result::error($notification);
        $result->getErrors()->addError($anotherError);

        $this->assertCount(1, $result->getErrors()->getErrors());
    }

    public function testEitherOnOkResult(): void
    {
        $value = 'ok';
        $result = Result::ok($value);

        $closureResult = $result->either(
            static fn (string $value): string => $value,
            static fn (Notification $notification): string => 'failed',
        );

        $this->assertEquals($value, $closureResult);
    }

    public function testEitherOnErrorResult(): void
    {
        $error = new Error('code', 'error');
        $result = Result::error(new Notification($error));

        $closureResult = $result->either(
            static fn (string $value): string => 'ok',
            static fn (Notification $notification): string => $notification->getErrors()[0]->errorMessage,
        );

        $this->assertEquals($error->errorMessage, $closureResult);
    }
}
