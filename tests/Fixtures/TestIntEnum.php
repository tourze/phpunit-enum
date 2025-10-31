<?php

namespace Tourze\PHPUnitEnum\Tests\Fixtures;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\Labelable;

enum TestIntEnum: int implements Labelable, Itemable
{
    case ACTIVE = 1;
    case INACTIVE = 2;
    case PENDING = 3;

    public function getLabel(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active Status',
            self::INACTIVE => 'Inactive Status',
            self::PENDING => 'Pending Status',
        };
    }

    public function toSelectItem(): array
    {
        return [
            'value' => $this->value,
            'label' => $this->getLabel(),
        ];
    }
}
