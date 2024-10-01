<?php

declare(strict_types=1);

namespace Dagger\Tests\Unit\Fixture\Enum;

use Dagger\Attribute;
use Dagger\ValueObject;

/**
 * An enum created to test SDK handling of custom enums
 */
#[Attribute\DaggerObject]
enum StringBackedDummy: string
{
    case Hello = 'hello';
    case World = 'world';

    public static function getValueObjectEquivalent(): ValueObject\DaggerObject
    {
        return new ValueObject\DaggerObject(self::class, '');
    }
}
