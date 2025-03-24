<?php

declare(strict_types=1);

namespace Dagger\Service\Serialisation;

use Dagger\TypeDefKind;
use Dagger\ValueObject\ListOfType;
use Dagger\ValueObject\Type;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

final readonly class Serialiser
{
    private Serializer $serializer;

    /**
     * @param \JMS\Serializer\EventDispatcher\EventSubscriberInterface[] $subscribers
     * @param \JMS\Serializer\Handler\SubscribingHandlerInterface[] $handlers
     */
    public function __construct(array $subscribers = [], array $handlers = [])
    {
        $this->serializer = SerializerBuilder::create()
            ->configureListeners(
                function (EventDispatcher $dispatcher) use ($subscribers) {
                    foreach ($subscribers as $subscriber) {
                        $dispatcher->addSubscriber($subscriber);
                    }
                }
            )
            ->configureHandlers(
                function (HandlerRegistry $registry) use ($handlers) {
                    foreach ($handlers as $handler) {
                        $registry->registerSubscribingHandler($handler);
                    }
                }
            )
            ->addDefaultHandlers()
            ->build();
    }

    public function serialise(mixed $value): string
    {
        return $this->serializer->serialize(
            $value,
            'json',
            SerializationContext::create()->setSerializeNull(true),
        );
    }

    public function deserialise(string $value, ListOfType|Type $type): mixed
    {
        if (in_array($value, ['', 'null'], true)) {
            return $type->nullable || $type->typeDefKind === TypeDefKind::VOID_KIND ?
                null :
                throw new \RuntimeException('null given for non-nullable type');
        }

        if ($type instanceof ListOfType) {
            return $this->deserialiseListOfType($value, $type);
        }

        return $this->serializer->deserialize($value, $type->name, 'json');
    }

    /** @return array<scalar> */
    private function deserialiseListOfType(string $value, ListOfType $list): array
    {
        if (preg_match('#^\[.*]$#', $value) !== 1) {
            throw new \RuntimeException(sprintf(
                '"%s" has unbalanced square brackets',
                $value,
            ));
        }

        $valueWithoutOuterBrackets = substr($value, 1, strlen($value) - 2);

        return array_map(
            fn($v) => $this->deserialise($v, $list->subtype),
            explode(',', $valueWithoutOuterBrackets),
        );
    }
}
