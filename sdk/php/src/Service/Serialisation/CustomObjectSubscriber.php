<?php

declare(strict_types=1);

namespace Dagger\Service\Serialisation;

use Dagger\Client\IdAble;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;

final readonly class CustomObjectSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            //[
            //    'event' => 'serializer.pre_serialize',
            //    'method' => 'onPreSerialize',
            //    'format' => 'json',
            //    'priority' => 0,
            //],
            [
                'event' => 'serializer.pre_deserialize',
                'method' => 'onPreDeserialize',
                'format' => 'json',
                'priority' => 0,
            ],
        ];
    }

    public function onPreSerialize(PreSerializeEvent $event): void
    {
    }

    public function onPreDeserialize(PreDeserializeEvent $event): void
    {
    }
}
