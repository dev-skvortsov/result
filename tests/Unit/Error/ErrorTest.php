<?php

declare(strict_types=1);

namespace Skd\Result\Tests\Unit\Error;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Skd\Result\Error\Error;

/**
 * @internal
 *
 * @coversNothing
 */
#[CoversClass(Error::class)]
class ErrorTest extends TestCase
{
    public function testEqualErrors(): void
    {
        $code = 'code';
        $message = 'message';

        $error = new Error($code, $message);

        $equalError = new Error($code, $message);
        $equalErrorWithExtra = new Error($code, $message, ['extra' => 'extra']);

        $this->assertTrue($error->isEqual($equalError));
        $this->assertTrue($error->isEqual($equalErrorWithExtra));
    }

    public function testNotEqualErrors(): void
    {
        $code = 'code';
        $message = 'message';

        $error = new Error($code, $message);

        $differentCode = new Error('diffCode', $message);
        $differentMessage = new Error($code, 'different message');
        $differentCodeAndMessage = new Error('diffCode', 'different message');

        $this->assertFalse($error->isEqual($differentCode));
        $this->assertFalse($error->isEqual($differentMessage));
        $this->assertFalse($error->isEqual($differentCodeAndMessage));
    }
}
