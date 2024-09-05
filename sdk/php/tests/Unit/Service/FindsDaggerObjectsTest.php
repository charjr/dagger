<?php

namespace Dagger\Tests\Unit\Service;

use Dagger\Service\FindsDaggerObjects;
use Dagger\Tests\Unit\Fixture\DaggerObjectWithDaggerFunctions;
use Dagger\Tests\Unit\Fixture\Nested\SimpleDaggerObject;
use Dagger\Tests\Unit\Fixture\NoDaggerFunctions;
use Dagger\ValueObject\DaggerObject;
use Generator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[Group('unit')]
#[CoversClass(FindsDaggerObjects::class)]
class FindsDaggerObjectsTest extends TestCase
{
    #[Test]
    public function itMustReceiveADirectory(): void {
        $sut = new FindsDaggerObjects();

        self::expectException(RuntimeException::class);

        $sut('');
    }

    /** @param DaggerObject[] $expected */
    #[Test, DataProvider('provideDirectoriesToSearch')]
    public function itFindsDaggerObjects(array $expected, string $dir): void {
        $actual = (new FindsDaggerObjects())($dir);

        self::assertEqualsCanonicalizing($expected, $actual);
    }

    /** @return Generator<array{ 0: DaggerObject[], 1: string}> */
    public static function provideDirectoriesToSearch(): Generator
    {
        yield 'test fixtures' => [
            [
                NoDaggerFunctions::getValueObjectEquivalent(),
                DaggerObjectWithDaggerFunctions::getValueObjectEquivalent(),
                SimpleDaggerObject::getValueObjectEquivalent(),
            ],
            __DIR__ . '/../Fixture',
        ];
    }
}
