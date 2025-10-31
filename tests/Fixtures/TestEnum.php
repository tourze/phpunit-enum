<?php

namespace Tourze\PHPUnitEnum\Tests\Fixtures;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\Labelable;

enum TestEnum: string implements Labelable, Itemable
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case PENDING = 'pending';

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
