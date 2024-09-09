<?php

declare(strict_types=1);

namespace Dagger\Service\Serialisation;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;

final readonly class BackedEnumHandler implements SubscribingHandlerInterface
{
    /**
     * @return array<array{
     *     direction: 1|2,
     *     format: string,
     *     type: string,
     *     method: string,
     * }>
     */
    public static function getSubscribingMethods(): array
    {
        return [
            [
                'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => \BackedEnum::class,
                'method' => 'serialise',
            ],
            [
                'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
                'format' => 'json',
                'type' => \BackedEnum::class,
                'method' => 'deserialise'
            ],
        ];
    }

    public function serialise(
        JsonSerializationVisitor $visitor,
        \BackedEnum|null $enum,
        array $type,
        Context $context,
    ): int|string {
        return $enum?->value;
    }

    public function deserialise(
        JsonDeserializationVisitor $visitor,
        ?string $enumValue,
        array $type,
        Context $context,
    ): \BackedEnum|null {
        if ($enumValue === null) {
            return null;
        }

        $className = $type['params'][BackedEnumSubscriber::ORIGINAL_CLASS_NAME] ??
            throw new \RuntimeException(
                'Cannot find original class name.' .
                ' If this issue occurs, it is a bug',
            );

        if (!in_array(\BackedEnum::class, class_implements($className) ?: [])) {
            throw new \RuntimeException(
                "'$className' was expected to be an enum." .
                ' If this issue occurs, it is a bug',
            );
        }

        return $className::from($enumValue);
    }
}
