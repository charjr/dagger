<?php

declare(strict_types=1);

namespace Dagger\Service;

use Dagger\Attribute;
use Dagger\ValueObject;
use Roave\BetterReflection\BetterReflection;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflector\DefaultReflector;
use Roave\BetterReflection\SourceLocator\Type\DirectoriesSourceLocator;

final class FindsDaggerObjects
{
    /**
     * Finds all classes with the DaggerObject attribute.
     * Only looks within the given directory.
     * @return array<ValueObject\DaggerObject|ValueObject\DaggerEnum>
     */
    public function __invoke(string $dir): array
    {
        $reflector = new DefaultReflector(new DirectoriesSourceLocator(
            [$dir],
            (new BetterReflection())->astLocator()
        ));

        $daggerObjects = array_filter(
            $reflector->reflectAllClasses(),
            fn($class) => $this->isDaggerObject($class)
        );

        return array_values(array_map(function ($className) {
            return enum_exists($className) ?
                ValueObject\DaggerEnum::fromReflection(new \ReflectionEnum(
                    $className
                )) :
                ValueObject\DaggerObject::fromReflection(new \ReflectionClass(
                    $className
                ));
        }, array_map(fn($do) => $do->getName(), $daggerObjects)));
    }

    private function isDaggerObject(ReflectionClass $class): bool
    {
        return !empty($class->getAttributesByName(Attribute\DaggerObject::class));
    }
}
