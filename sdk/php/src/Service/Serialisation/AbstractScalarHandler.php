<?php

declare(strict_types=1);

namespace Dagger\Service\Serialisation;

use Dagger\Client\AbstractScalar;
use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;
use RuntimeException;

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

    /**
     * @param array{
     *     name: string,
     *     params: array<string, mixed>
     * } $type
     */
    public function serialise(
        JsonSerializationVisitor $visitor,
        AbstractScalar $abstractScalar,
        array $type,
        Context $context
    ): string {
        return (string) $abstractScalar;
    }

    /**
     * @param array{
     *     name: string,
     *     params: array<string, mixed>
     * } $type
     */
    public function deserialise(
        JsonDeserializationVisitor $visitor,
        string $abstractScalar,
        array $type,
        Context $context
    ): AbstractScalar {
        $originalClassName = $type['params'][
            AbstractScalarSubscriber::ORIGINAL_CLASS_NAME
        ];

        if (!$originalClassName instanceof AbstractScalar) {
            throw new RuntimeException(sprintf(
                '"%s" does not extend "%s".' .
                'It cannot be deserialised by "%s".' .
                'If this exception occurs it is likely to be a bug',
                $originalClassName,
                AbstractScalar::class,
                self::class,
            ));
        }

        return new $originalClassName($abstractScalar);
    }
}
