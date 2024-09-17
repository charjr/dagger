<?php

declare(strict_types=1);

namespace Dagger\ValueObject;

use Dagger\Attribute;
use Dagger\Client\IdAble;
use Dagger\Exception\UnsupportedType;
use Dagger\Json;
use ReflectionNamedType;
use ReflectionParameter;
use Roave\BetterReflection\Reflection\ReflectionParameter as BetterReflectionParameter;
use RuntimeException;

final readonly class Argument
{
    public function __construct(
        public string $name,
        public ?string $description,
        public TypeHint $type,
        public ?Json $default = null,
    ) {
    }

    public static function fromReflection(ReflectionParameter $parameter): self
    {
        $argument = (current($parameter
            ->getAttributes(Attribute\Argument::class)) ?: null)
            ?->newInstance();

        return new self(
            $parameter->name,
            $argument?->description,
            self::getType($parameter),
            self::getDefault($parameter),
        );
    }

    private static function getType(ReflectionParameter $parameter): TypeHint
    {
        $type = $parameter->getType() ??
            throw new RuntimeException(sprintf(
                'Argument "%s" cannot be supported without a typehint',
                $parameter->name,
            ));

        if (!$type instanceof ReflectionNamedType) {
            throw new UnsupportedType(sprintf(
                'Argument %s with unsupported type, ' .
                ' intersection and union types are not supported',
                $parameter->name,
            ));
        }

        $name = $type->getName();

        if (enum_exists($name)) {
            return EnumType::fromReflection($type);
        }

        if ($name === 'array') {
            $attribute = (current($parameter
                ->getAttributes(Attribute\ReturnsListOfType::class)) ?: null)
                ?->newInstance() ??
                throw new RuntimeException(sprintf(
                    '%s requires a %s attribute because it returns an array',
                    $parameter->name,
                    Attribute\ReturnsListOfType::class,
                ));

            return ListOfType::fromReflection($type, $attribute);
        }

        return Type::fromReflection($type);
    }

    private static function getDefault(ReflectionParameter $parameter): ?Json
    {
        if ($parameter->isDefaultValueAvailable()) {
            $default = $parameter->getDefaultValue();
            return new Json(json_encode(
                $default instanceof IdAble ? (string) $default->id() : $default
            ));
        }

        if ($parameter->allowsNull()) {
            return new Json(json_encode(null));
        }

        return null;
    }
}
