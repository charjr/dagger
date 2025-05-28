<?php

declare(strict_types=1);

namespace Dagger\Service\Serialisation;

use Dagger\Client;
use Dagger\Client\IdAble;
use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;
use ReflectionClass;
use RuntimeException;

final readonly class CustomObjectHandler implements SubscribingHandlerInterface
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
            //[
            //    'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
            //    'format' => 'json',
            //    'type' => 'object',
            //    'method' => 'serialise',
            //],
            [
                'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
                'format' => 'json',
                'type' => \stdClass::class,
                'method' => 'deserialise'
            ],
        ];
    }

    //public function serialise(
    //    JsonSerializationVisitor $visitor,
    //    object $customObject,
    //    array $type,
    //    Context $context
    //): string {
    //    return (string) $idAble->id();
    //}

    public function deserialise(
        JsonDeserializationVisitor $visitor,
        string $customObject,
        array $type,
        Context $context,
    ): object {
        throw new RuntimeException($customObject);
        var_dump($customObject);

        return $customObject;
    }
}
