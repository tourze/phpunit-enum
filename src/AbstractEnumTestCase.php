<?php

declare(strict_types=1);

namespace Tourze\PHPUnitEnum;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\Labelable;
use Tourze\PHPUnitEnum\Exception\EnumTestException;

/**
 * 通用的枚举测试
 */
#[Small]
abstract class AbstractEnumTestCase extends TestCase
{
    /**
     * 这个场景，没必要使用 RunTestsInSeparateProcesses 注解的
     */
    #[Test]
    final public function testShouldNotHaveRunTestsInSeparateProcesses(): void
    {
        $reflection = new \ReflectionClass(get_class($this));
        $this->assertEmpty($reflection->getAttributes(RunTestsInSeparateProcesses::class), get_class($this) . '这个测试用例，不应使用 RunTestsInSeparateProcesses 注解');
    }

    /**
     * 为给定枚举生成无效值
     */
    private static function generateInvalidValue(\BackedEnum $case): string|int
    {
        if (is_string($case->value)) {
            // 字符串枚举：生成不存在的字符串值
            return 'invalid_' . $case->value . '_' . random_int(1, 1000);
        }

        // 整数枚举：生成不在枚举值中的整数
        /** @var class-string<\BackedEnum>&literal-string $className */
        $className = $case::class;
        $cases = $className::cases();
        $allValues = array_map(fn ($c) => $c->value, $cases);

        if (0 === count($allValues)) {
            return -1; // 默认无效值
        }

        $min = (int) min($allValues);
        $max = (int) max($allValues);

        // 生成不在范围内的值
        if ($min > $max) {
            return $min - 1;
        }

        $range = $max - $min;
        if ($range < 200) {
            $testValue = $max + 1;
            if (!in_array($testValue, $allValues, true)) {
                return $testValue;
            }

            return $min - 1;
        }

        // 大范围随机选择
        do {
            $testValue = random_int($min - 100, $max + 100);
        } while (in_array($testValue, $allValues, true));

        return $testValue;
    }

    /**
     * 确保是枚举类
     */
    #[Test]
    final public function testEnumClass(): void
    {
        $className = static::getEnumClass();
        $this->assertTrue(enum_exists($className));
    }

    /**
     * 当前要测试的枚举类
     *
     * @return class-string<\BackedEnum>
     */
    final protected static function getEnumClass(): string
    {
        $reflection = new \ReflectionClass(get_called_class());
        foreach ($reflection->getAttributes(CoversClass::class) as $attribute) {
            $covers = $attribute->newInstance();
            if (!$covers instanceof CoversClass) {
                continue;
            }
            $className = $covers->className();
            if (!\is_a($className, \BackedEnum::class, true)) {
                throw new EnumTestException(sprintf('被 CoversClass 指定的类 %s 不是 BackedEnum 枚举', $className));
            }
            /** @var class-string<\BackedEnum> $className */
            return $className;
        }
        throw new EnumTestException('无法根据测试用例定位关联枚举类，请使用 CoversClass 注解声明关联枚举类');
    }

    #[DataProvider('validValueProvider')]
    #[Test]
    final public function testValidValue(\BackedEnum $enum, string|int $value): void
    {
        $this->assertSame($value, $enum->value);
    }

    #[DataProvider('validValueProvider')]
    #[Test]
    final public function testFromValid(\BackedEnum $enum, string|int $value): void
    {
        $newEnum = $enum::from($value);
        $this->assertSame($newEnum, $enum);
    }

    #[DataProvider('validValueProvider')]
    #[Test]
    final public function testTryFromValid(\BackedEnum $enum, string|int $value): void
    {
        $newEnum = $enum::tryFrom($value);
        $this->assertSame($newEnum, $enum);
    }

    /**
     * @return iterable<array{\BackedEnum, string|int}>
     */
    final public static function validValueProvider(): iterable
    {
        $className = static::getEnumClass();
        /** @var \BackedEnum $case */
        foreach ($className::cases() as $case) {
            yield [$case, $case->value];
        }
    }

    #[DataProvider('validLabelProvider')]
    #[Test]
    final public function testValidLabel(\BackedEnum&Labelable $enum, string $label): void
    {
        $this->assertSame($label, $enum->getLabel());
    }

    /**
     * @return iterable<array{\BackedEnum&Labelable, string}>
     */
    final public static function validLabelProvider(): iterable
    {
        $className = static::getEnumClass();
        /** @var \BackedEnum $case */
        foreach ($className::cases() as $case) {
            if ($case instanceof Labelable) {
                yield [$case, $case->getLabel()];
            }
        }
    }

    #[DataProvider('invalidValueProvider')]
    #[Test]
    final public function testFromInvalid(\BackedEnum $enum, string|int $value): void
    {
        $this->expectException(\ValueError::class);
        $v = $enum::from($value);
        $this->assertNotSame($value, $v->value);
    }

    #[DataProvider('invalidValueProvider')]
    #[Test]
    final public function testTryFromInvalid(\BackedEnum $enum, string|int $value): void
    {
        $v = $enum::tryFrom($value);
        $this->assertNull($v);
    }

    /**
     * @return iterable<array{\BackedEnum, string|int}>
     */
    final public static function invalidValueProvider(): iterable
    {
        $className = static::getEnumClass();
        /** @var \BackedEnum $case */
        foreach ($className::cases() as $case) {
            $invalidValue = self::generateInvalidValue($case);
            yield [$case, $invalidValue];
        }
    }

    final public function testToSelectItem(): void
    {
        $className = static::getEnumClass();
        foreach ($className::cases() as $case) {
            /** @var \BackedEnum $case */
            $this->assertInstanceOf(Itemable::class, $case);
            $this->assertIsArray($case->toSelectItem());
        }
    }
}
