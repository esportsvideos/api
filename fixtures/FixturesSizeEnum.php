<?php

namespace DataFixtures;

enum FixturesSizeEnum: string
{
    case NONE = 'none';
    case SMALL = 'small';
    case MEDIUM = 'medium';
    case LARGE = 'large';
    case XL = 'xl';
    case XXL = 'xxl';

    public function getFixtureSize(): int
    {
        return match ($this) {
            self::SMALL => 10,
            self::MEDIUM => 100,
            self::LARGE => 1000,
            self::XL => 10000,
            self::XXL => 100000,
            default => 0,
        };
    }
}
