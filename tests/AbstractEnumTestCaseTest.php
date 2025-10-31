<?php

namespace Tourze\PHPUnitEnum\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Test;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use Tourze\PHPUnitEnum\Tests\Fixtures\TestEnum;

/**
 * @internal
 */
#[Small]
#[CoversClass(TestEnum::class)]
class AbstractEnumTestCaseTest extends AbstractEnumTestCase
{
    #[Test]
    public function testCanGetEnumClass(): void
    {
        $enumClass = self::getEnumClass();
        $this->assertSame(TestEnum::class, $enumClass);
    }

    #[Test]
    public function testValidValueProviderReturnsCorrectData(): void
    {
        $data = iterator_to_array(self::validValueProvider());

        $this->assertCount(3, $data);

        foreach ($data as $item) {
            $this->assertIsArray($item);
            $this->assertCount(2, $item);
            $this->assertInstanceOf(\BackedEnum::class, $item[0]);
            $this->assertIsString($item[1]);
        }
    }

    #[Test]
    public function testValidLabelProviderReturnsCorrectData(): void
    {
        $data = iterator_to_array(self::validLabelProvider());

        $this->assertCount(3, $data);

        foreach ($data as $item) {
            $this->assertIsArray($item);
            $this->assertCount(2, $item);
            $this->assertInstanceOf(\BackedEnum::class, $item[0]);
            $this->assertIsString($item[1]);
        }
    }

    #[Test]
    public function testInvalidValueProviderReturnsCorrectData(): void
    {
        $data = iterator_to_array(self::invalidValueProvider());

        $this->assertCount(3, $data);

        foreach ($data as $item) {
            $this->assertIsArray($item);
            $this->assertCount(2, $item);
            $this->assertInstanceOf(\BackedEnum::class, $item[0]);

            // 对于字符串枚举，无效值也应该是字符串
            if (is_string($item[0]->value)) {
                $this->assertIsString($item[1]);
                $this->assertStringContainsString('invalid_', $item[1]);
            } else {
                $this->assertIsInt($item[1]);
            }
        }
    }
}
