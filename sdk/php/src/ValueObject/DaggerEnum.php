<?php

declare(strict_types=1);

namespace Dagger\ValueObject;

use Dagger\Attribute\DaggerObject;

final readonly class DaggerEnum
{
    /** @param string[] $cases */
    public function __construct(
        public string $name,
        public array $cases,
    ) {
    }

    public static function fromReflection(\ReflectionEnum $enum): self
    {
        if (empty($enum->getAttributes(DaggerObject::class))) {
            throw new \RuntimeException('class is not a DaggerObject');
        }

        return new self(
            $enum->name,
            array_map(fn($c) => $c->name, $enum->getCases())
        );
    }
}
