<?php

declare(strict_types=1);

namespace Dagger\Service\Serialisation;

use Dagger\ValueObject\ListOfType;
use Dagger\ValueObject\Type;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

final readonly class ValueToJson
{
    private Serializer $serializer;

    public function __construct(
        SubscribingHandlerInterface ...$handlers,
    ) {
        $this->serializer = SerializerBuilder::create()
            ->configureHandlers(
                function(HandlerRegistry $registry) use ($handlers) {
                    foreach ($handlers as $handler) {
                        $registry->registerSubscribingHandler($handler);
                    }
                }
            )
            ->addDefaultHandlers()
            ->build();
    }

    public function serialize(mixed $value): string
    {
        return $this->serializer->serialize(
            $value,
            'json',
            SerializationContext::create()->setSerializeNull(true),
        );
    }

    public function deserialize(string $value, string $className): object
    {
        return $this->serializer->deserialize(
            $value,
            $className,
            'json',
        );
    }
}
