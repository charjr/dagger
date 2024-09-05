<?php

declare(strict_types=1);

namespace Dagger\Tests\Unit\Fixture\Enum;

/**
 * An enum created to test SDK handling of custom enums
 */
enum StringBackedDummy: string
{
    case Hello = 'hello';
    case World = 'world';
}
