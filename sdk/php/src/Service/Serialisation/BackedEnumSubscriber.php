<?php

declare(strict_types=1);

namespace Dagger\Service\Serialisation;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;

final readonly class BackedEnumSubscriber implements EventSubscriberInterface
{
    public const ORIGINAL_CLASS_NAME =
        'The original class name before ' .
        'being changed to ' .
        \BackedEnum::class;

    public static function getSubscribedEvents(): array
    {
        return [
            [
                'event' => 'serializer.pre_serialize',
                'method' => 'onPreSerialize',
                'interface' => \BackedEnum::class,
            ],
            [
                'event' => 'serializer.pre_deserialize',
                'method' => 'onPreDeserialize',
            ],
        ];
    }

    public function onPreSerialize(PreSerializeEvent $event): void
    {
        if ($event->getObject() instanceof \BackedEnum) {
            $event->setType(\BackedEnum::class);
        }
    }

    public function onPreDeserialize(PreDeserializeEvent $event): void
    {
        $className = $event->getType()['name'];

        if (!enum_exists($className)) {
            return;
        }

        $event->setType(\BackedEnum::class, array_merge_recursive(
            $event->getType()['params'],
            [self::ORIGINAL_CLASS_NAME => $className]
        ));
    }
}
