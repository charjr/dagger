<?php

declare(strict_types=1);

namespace Dagger\Service\Serialisation;

use Dagger;
use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;

final readonly class IdableHandler implements SubscribingHandlerInterface
{
    public function __construct(
        private Dagger\Client $dag,
    ) {
    }

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
                'type' => Dagger\Client\IdAble::class,
                'method' => 'serialiseIdable'
            ],
            [
                'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
                'format' => 'string',
                'type' => Dagger\Client\IdAble::class,
                'method' => 'serialiseIdable'
            ],
        ];

    }

    public function serialiseIdable(
        JsonSerializationVisitor $visitor,
        Dagger\Client\IdAble $idable,
        array $type,
        Context $context
    ): string {
        return json_encode((string) $idable->id());
    }

    public function deserialiseIdable(
        JsonSerializationVisitor $visitor,
        string $id,
        array $type,
        Context $context
    ): Dagger\Client\Idable {
        var_dump($type);
    }
}
