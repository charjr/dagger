<?php

declare(strict_types=1);

namespace Dagger\Service\Serialisation;

use Dagger\Client\AbstractScalar;
use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;

final readonly class AbstractScalarHandler implements SubscribingHandlerInterface
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
                'type' => AbstractScalar::class,
                'method' => 'serialise',
            ],
            [
                'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
                'format' => 'json',
                'type' => AbstractScalar::class,
                'method' => 'deserialise'
            ],
        ];

    }

    public function serialise(
        JsonSerializationVisitor $visitor,
        AbstractScalar $abstractScalar,
        array $type,
        Context $context
    ): string {
        return (string) $abstractScalar;
    }

    public function deserialise(
        JsonSerializationVisitor $visitor,
        string $abstractScalar,
        array $type,
        Context $context
    ): AbstractScalar {
        var_export($type);

        return \Dagger\Platform::from($abstractScalar);
    }
}
