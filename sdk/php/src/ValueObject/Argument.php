<?php

declare(strict_types=1);

namespace Dagger\ValueObject;

use Dagger\Attribute;
use Dagger\Client\IdAble;
use Dagger\Json;
use Dagger\Service\Serialisation;
use ReflectionParameter;
use Roave\BetterReflection\Reflection\ReflectionParameter as BetterReflectionParameter;
use RuntimeException;

final readonly class Argument
{
    public function __construct(
        public string $name,
        public ?string $description,
        public ListOfType|Type $type,
        public ?Json $default = null,
    ) {
    }

    public static function fromReflection(ReflectionParameter $parameter): self
    {
        $type = $parameter->getType() ??
            throw new RuntimeException(sprintf(
                'Argument "%s" cannot be supported without a typehint',
                $parameter->name,
            ));

        $argument = (current($parameter
            ->getAttributes(Attribute\Argument::class)) ?: null)
            ?->newInstance();

        $listOfType = (current($parameter
            ->getAttributes(Attribute\ListOfType::class)) ?: null)
            ?->newInstance();

        return new self(
            $parameter->name,
            $argument?->description,
            $listOfType?->type === null ?
                Type::fromReflection($type) :
                ListOfType::fromReflection($type, $listOfType),
            self::getDefault($parameter),
        );
    }

    private static function getDefault(ReflectionParameter $parameter): ?Json
    {
        if ($parameter->isDefaultValueAvailable()) {
            $default = $parameter->getDefaultValue();

            if ($default instanceof IdAble) {
                return new Json(sprintf('"%s"', $default->id()));
            }

            return new Json(self::getSerialiser()->serialise($default));
        }

        if ($parameter->allowsNull()) {
            return new Json('null');
        }

        return null;
    }

    private static function getSerialiser(): Serialisation\Serialiser
    {
            return new Serialisation\Serialiser(
                [new Serialisation\AbstractScalarSubscriber()],
                [new Serialisation\AbstractScalarHandler()],
            );
    }
}
