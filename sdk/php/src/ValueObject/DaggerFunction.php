<?php

declare(strict_types=1);

namespace Dagger\ValueObject;

use Dagger\Attribute;
use ReflectionMethod;
use RuntimeException;

final readonly class DaggerFunction
{
    /** @param Argument[] $arguments */
    public function __construct(
        public string $name,
        public ?string $description,
        public array $arguments,
        public TypeHint $returnType,
    ) {
    }

    /**
     * @throws RuntimeException
     * - if missing DaggerFunction Attribute
     * - if any parameter types are unsupported
     * - if the return type is unsupported
     */
    public static function fromReflection(ReflectionMethod $method): self
    {
        $attribute = (current($method
            ->getAttributes(Attribute\DaggerFunction::class)) ?: null)
            ?->newInstance() ??
            throw new RuntimeException('method is not a DaggerFunction');

        $parameters = array_map(
            fn($p) => Argument::fromReflection($p),
            $method->getParameters(),
        );

        return new self(
            $method->name,
            $attribute->description,
            $parameters,
            self::getReturnType($method),
        );
    }

    private static function getReturnType(
        ReflectionMethod $method
    ): TypeHint {
        $type = $method->getReturnType() ??
            throw new RuntimeException(sprintf(
                'DaggerFunction "%s" cannot be supported without a return type',
                $method->name,
            ));

        $name = $type->getName();

        if (enum_exists($name)) {
            return EnumType::fromReflection($type);
        }

        if ($name === 'array') {
            $attribute = (current($method
                ->getAttributes(Attribute\ReturnsListOfType::class)) ?: null)
                ?->newInstance() ??
                throw new RuntimeException(sprintf(
                    '%s requires a %s attribute because it returns an array',
                    $method->name,
                    Attribute\ReturnsListOfType::class,
                ));

            return ListOfType::fromReflection($type, $attribute);
        }

        return Type::fromReflection($type);
    }
}
