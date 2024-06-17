<?php

declare(strict_types=1);

namespace Dagger\Tests\Unit\Fixture;

use Dagger\Attribute\DaggerObject;

#[DaggerObject]
final class NoDaggerFunctions
{
    public function notDaggerFunction(): bool
    {
        return true;
    }
}
