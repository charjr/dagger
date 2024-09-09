<?php

namespace Dagger\Tests\Unit\Service\Serialisation;

use Dagger\Client;
use Dagger\ContainerId;
use Dagger\Json;
use Dagger\NetworkProtocol;
use Dagger\Platform;
use Dagger\Service\Serialisation\AbstractScalarHandler;
use Dagger\Service\Serialisation\AbstractScalarSubscriber;
use Dagger\Service\Serialisation\BackedEnumHandler;
use Dagger\Service\Serialisation\BackedEnumSubscriber;
use Dagger\Service\Serialisation\Serialiser;
use Dagger\Tests\Unit\Fixture\DaggerObject\HandlingEnums;
use Dagger\Tests\Unit\Fixture\Enum\StringBackedDummy;
use Generator;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
#[CoversClass(Serialiser::class)]
class SerialiserTest extends TestCase
{
    /**
     * @param EventSubscriberInterface[] $subscribers
     * @param SubscribingHandlerInterface[] $handlers
     */
    #[Test]
    #[DataProvider('provideEnums')]
    #[DataProvider('provideScalars')]
    #[DataProvider('provideListsOfScalars')]
    #[DataProvider('provideAbstractScalars')]
    public function itSerialisesValues(
        array $subscribers,
        array $handlers,
        mixed $value,
        string $valueAsJSON,
    ): void {
        $sut = new Serialiser($subscribers, $handlers);

        self::assertSame($valueAsJSON, $sut->serialise($value));
    }

    /**
     * @param EventSubscriberInterface[] $subscribers
     * @param SubscribingHandlerInterface[] $handlers
     */
    #[Test]
    #[DataProvider('provideEnums')]
    #[DataProvider('provideScalars')]
    #[DataProvider('provideListsOfScalars')]
    #[DataProvider('provideAbstractScalars')]
    public function itDeserialisesValues(
        array $subscribers,
        array $handlers,
        mixed $value,
        string $valueAsJSON,
        string $type,
    ): void {
        $sut = new Serialiser($subscribers, $handlers);

        self::assertEquals($value, $sut->deserialise($valueAsJSON, $type));
    }

        /**
     * @return Generator<array{
     *     0: array{ 0:BackedEnumSubscriber },
     *     1: array{ 0:BackedEnumHandler },
     *     2: mixed,
     *     3: string,
     *     4: string,
     * }>
     */
    public static function provideEnums(): Generator
    {
        $cases = [
            NetworkProtocol::class => [NetworkProtocol::TCP, '"TCP"'],
            StringBackedDummy::class => [StringBackedDummy::Hello, '"hello"'],
        ];

        foreach ($cases as $case => [$value, $valueAsJson]) {
            yield $value::class => [
                [new BackedEnumSubscriber()],
                [new BackedEnumHandler()],
                $value,
                $valueAsJson,
                $value::class,
            ];
        }
    }

    /**
     * @return Generator<array{
     *     0: array{},
     *     1: array{},
     *     2: mixed,
     *     3: string,
     *     4: string,
     * }>
     */
    public static function provideScalars(): Generator
    {
        foreach ([true, false, 123, null, 'Hello, World!', 'null'] as $case) {
            $type = gettype($case);
            yield "($type) $case" => [[], [], $case, json_encode($case), $type];
        }

        yield 'expecting int, receive null' => [[], [], null, 'null', 'int'];
    }

    /**
     * @return Generator<array{
     *     0: array{},
     *     1: array{},
     *     2: mixed,
     *     3: string,
     *     4: string,
     * }>
     */
    public static function provideListsOfScalars(): Generator
    {
        $cases = [
            'string[]' => [['hello', 'world'], '["hello","world"]'],
            'expecting array, receive null' => [null, 'null'],
            'null[]' => [[null, null], '[null,null]'],
        ];

        foreach ($cases as $case => [$value, $valueAsJson]) {
            yield $case => [[], [], $value, $valueAsJson, 'array'];
        }
    }

    /**
     * @return Generator<array{
     *     0: array{ 0:AbstractScalarSubscriber },
     *     1: array{ 0:AbstractScalarHandler },
     *     2: mixed,
     *     3: string,
     *     4: string,
     * }>
     */
    public static function provideAbstractScalars(): Generator
    {
        $cases = [
            [new Platform('linux/amd64'), '"linux\/amd64"'],
            [new Json('{"bool_field":true}'), '"{\"bool_field\":true}"'],
            [new ContainerId('1234-567-89'), '"1234-567-89"'],
        ];

        foreach ($cases as [$value, $valueAsJson]) {
            yield $value::class => [
                [new AbstractScalarSubscriber()],
                [new AbstractScalarHandler()],
                $value,
                $valueAsJson,
                $value::class,
            ];
        }
    }
}
