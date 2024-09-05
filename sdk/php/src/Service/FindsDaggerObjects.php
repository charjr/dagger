<?php

declare(strict_types=1);

namespace Dagger\Service;

use Dagger\Attribute;
use Dagger\ValueObject;
use Roave\BetterReflection\BetterReflection;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflector\DefaultReflector;
use Roave\BetterReflection\SourceLocator\Type\DirectoriesSourceLocator;
use RuntimeException;

final class FindsDaggerObjects
{
    /**
     * Finds all classes with the DaggerObject attribute.
     * Only looks within the given directory.
     * @return ValueObject\DaggerObject[]
     */
    public function __invoke(string $dir): array
    {
        if (!is_dir($dir)) {
            throw new RuntimeException("'$dir' is not a directory");
        }

        $reflector = new DefaultReflector(new DirectoriesSourceLocator(
            [$dir],
            (new BetterReflection())->astLocator()
        ));

        $daggerObjects = array_filter(
            $reflector->reflectAllClasses(),
            fn($class) => $this->isDaggerObject($class)
        );

        return array_values(array_map(
            fn($d) => ValueObject\DaggerObject::fromReflection(
                new \ReflectionClass($d->getName())
            ),
            $daggerObjects
        ));
    }

    private function isDaggerObject(ReflectionClass $class): bool
    {
        return !empty($class->getAttributesByName(Attribute\DaggerObject::class));
    }
}
