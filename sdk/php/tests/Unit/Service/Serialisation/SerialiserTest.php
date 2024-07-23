<?php

namespace Dagger\tests\Unit\Service\Serialisation;

use Dagger\Client;
use Dagger\Platform;
use Dagger\Service\Serialisation\AbstractScalarHandler;
use Dagger\Service\Serialisation\AbstractScalarSubscriber;
use Dagger\Service\Serialisation\Serialiser;
use Dagger\ValueObject\ListOfType;
use Dagger\ValueObject\Type;
use Generator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
#[CoversClass(Serialiser::class)]
class SerialiserTest extends TestCase
{
    #[Test]
    #[DataProvider('provideScalars')]
    #[DataProvider('provideLists')]
    public function itSerialisesValuesToJson(mixed $value, string $valueAsJSON)
    : void {
        self::assertSame($valueAsJSON, (new Serialiser())->serialise($value));
    }

//    #[Test]
//    #[DataProvider('provideScalars')]
//    #[DataProvider('provideLists')]
//    public function itDeserialisesJsonToValues(
//        string $value,
//        mixed $expected,
//    ): void {
//        $sut = new Serialiser();
//
//        $actual = $sut->deserialise($value);
//
//        self::assertSame($expected, $actual);
//    }

    /**
     * @param \JMS\Serializer\EventDispatcher\EventSubscriberInterface $subscribers,
     * @param SubscribingHandlerInterface[] $handlers
     */
    #[Test]
    #[DataProvider('provideAbstractScalars')]
    public function itCanSerializeAbstractScalars(
        Client\AbstractScalar $value,
        string $valueAsJSON,
        array $subscribers,
        array $handlers,
    ): void {
        $sut = new Serialiser($subscribers, $handlers);

        $actual = $sut->serialise($value);

        self::assertSame($valueAsJSON, $actual);
    }

//    /**
//     * @param SubscribingHandlerInterface[] $subscribingHandlers
//     */
//    #[Test]
//    #[DataProvider('provideAbstractScalars')]
//    public function itCanUseCustomHandlersForDeserialisation(
//        Client\AbstractScalar $value,
//        string $valueAsJSON,
//        array $subscribingHandlers,
//        string $className,
//    ): void {
//        $sut = new Serialiser(...$subscribingHandlers);
//
//        self::assertSame($valueAsJSON, $sut->deserialise($value, $className));
//    }

    /**
     * @return \Generator<array{
     *     0: string,
     *     1: mixed,
     * }>
     */
    public static function provideScalars(): Generator
    {
        yield '(bool) true' => [true, 'true'];

        yield '(bool) false' => [false, 'false'];

        yield '(int) 418' => [418, '418'];

        yield '(null) null' => [null, 'null'];

        yield '(string) "expected"' => ['expected', '"expected"'];

        yield '(void) null' => [null, 'null'];
    }

    /**
     * @return \Generator<array{
     *     0: string,
     *     1: mixed,
     * }>
     */
    public static function provideLists(): Generator
    {
        yield '[String]' => [['hello', 'world'], '["hello","world"]'];

        yield '[String], passed null' => [null, 'null'];

        yield '[String], passed array of null' => [[null, null], '[null,null]'];

        yield '[String]!' => [['hello', 'world'], '["hello","world"]'];

        yield '[String]!, passed array of null' => [[null, null], '[null,null]'];

        yield '[String!]' => [['hello', 'world'], '["hello","world"]'];

        yield '[String!], passed null' => [null, 'null'];

        yield '[String!]!' => [['hello', 'world'], '["hello","world"]'];
    }

    /**
     * @return \Generator<array{
     *     0: string,
     *     1: SubscribingHandlerInterface[],
     *     2: \Dagger\Client\AbstractScalar,
     * }>
     */
    public static function provideAbstractScalars(): Generator
    {
        $subscribers = [new AbstractScalarSubscriber()];
        $handlers = [new AbstractScalarHandler()];

        $classes = get_declared_classes();
        $scalarClasses = array_filter(
            $classes,
            fn($c) => $c instanceof Client\AbstractScalar
        );



        yield Platform::class => [
            new Platform('linux/amd64'),
            '"linux\/amd64"',
            $subscribers,
            $handlers,
            Platform::class,
        ];
    }
}
