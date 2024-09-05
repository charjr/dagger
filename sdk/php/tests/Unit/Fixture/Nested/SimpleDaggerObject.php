<?php

declare(strict_types=1);

namespace Dagger\Tests\Unit\Fixture\Nested;

use Dagger\Attribute\DaggerFunction;
use Dagger\Attribute\DaggerObject;
use Dagger\ValueObject;

#[DaggerObject]
final class SimpleDaggerObject
{
    #[DaggerFunction]
    public function simpleDaggerFunction(): bool
    {
        return true;
    }

    public static function getValueObjectEquivalent(): ValueObject\DaggerObject
    {
        return new ValueObject\DaggerObject(self::class, [
            'simpleDaggerFunction' => new ValueObject\DaggerFunction(
                'simpleDaggerFunction',
                null,
                [],
                new ValueObject\Type('bool'),
            )
        ]);
    }
}
