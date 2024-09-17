<?php

declare(strict_types=1);

namespace Dagger\ValueObject;

final class EnumType implements TypeHint
{
    public function __construct(
        public string $name,
        public bool $nullable = false,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTypeDefKind(): \Dagger\TypeDefKind
    {
        return \Dagger\TypeDefKind::ENUM_KIND;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public function getShortName(): string
    {
        $class = new \ReflectionClass($this->name);

        return $class->getShortName();
    }

    public static function fromReflection(\ReflectionType $type): self
    {
        if (!($type instanceof \ReflectionNamedType)) {
            throw new \Dagger\Exception\UnsupportedType(sprintf(
                'Currently the PHP SDK only supports %s',
                \ReflectionNamedType::class,
            ));
        }

        if (!enum_exists($type->getName())) {
            throw new \RuntimeException(sprintf(
                '%s is expected to be an enum',
                $type->getName(),
            ));
        }

        return new self($type->getName(), $type->allowsNull());
    }
}
