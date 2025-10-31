<?php

namespace Tourze\PHPUnitEnum\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Test;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use Tourze\PHPUnitEnum\Exception\EnumTestException;

/**
 * @internal
 */
#[Small]
#[CoversClass(EnumTestException::class)]
class EnumTestExceptionTest extends AbstractExceptionTestCase
{
    #[Test]
    public function testExceptionCanBeCreated(): void
    {
        $message = 'Test exception message';
        $exception = new EnumTestException($message);

        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertSame($message, $exception->getMessage());
    }

    #[Test]
    public function testExceptionCanBeCreatedWithCodeAndPrevious(): void
    {
        $message = 'Test exception message';
        $code = 123;
        $previous = new \RuntimeException('Previous exception');

        $exception = new EnumTestException($message, $code, $previous);

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }

    #[Test]
    public function testExceptionCanBeThrown(): void
    {
        $this->expectException(EnumTestException::class);
        $this->expectExceptionMessage('Test exception');

        throw new EnumTestException('Test exception');
    }
}
