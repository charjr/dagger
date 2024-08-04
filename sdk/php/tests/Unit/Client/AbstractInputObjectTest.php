<?php

namespace Dagger\tests\Unit\Client;

use Dagger\Client;
use Dagger\Client\AbstractInputObject;
use Dagger\Service\DecodesValue;
use Dagger\ValueObject\ListOfType;
use Dagger\ValueObject\Type;
use Generator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
#[CoversClass(AbstractInputObject::class)]
class AbstractInputObjectTest extends TestCase
{
    #[Test]
    #[DataProvider('provideInputObjects')]
    public function itIsStringable(
        string $expected,
        AbstractInputObject $sut,
    ): void {
        self::assertSame($expected, $sut->__toString());
    }

    /** @return \Generator<array{ 0: string, 1: AbstractInputObject }> */
    public static function provideInputObjects(): Generator
    {
        yield 'no properties' => [
            '{}',
            new class () extends AbstractInputObject {},
        ];

        yield 'one string property' => [
            '{value: "string value"}',
            new class () extends AbstractInputObject {
                public function __construct(
                    public string $value = 'string value'
                ) {}
            },
        ];

        yield 'one int property' => [
            '{value: 2}',
            new class () extends AbstractInputObject {
                public function __construct(
                    public int $value = 2
                ) {}
            },
        ];

        yield 'one list property' => [
            '{value: [1, 2, 3]}',
            new class () extends AbstractInputObject {
                public function __construct(
                    public array $value = [1, 2, 3]
                ) {}
            },
        ];

        yield 'three scalar properties' => [
            '{first: "string value", second: 2, third: true}',
            new class () extends AbstractInputObject {
                public function __construct(
                    public string $first = 'string value',
                    public int $second = 2,
                    public bool $third = true,
                ) {}
            },
        ];
    }
}
