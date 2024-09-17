<?php

namespace Dagger\tests\Unit\Service;

use Dagger\Service\RemovesNamespacePrefix;
use PHPUnit\Framework\Attributes\{CoversClass, DataProvider, Group, Test};

#[Group('unit')]
#[CoversClass(RemovesNamespacePrefix::class)]
class RemovesNamespacePrefixTest extends \PHPUnit\Framework\TestCase
{
    #[Test, DataProvider('provideClassNames')]
    public function itRemovesFirstPrefixFromClassName(
        string $className,
        string $expected,
    ): void {
        self::assertSame($expected, (new RemovesNamespacePrefix())($className));
    }

    /** @return \Generator<array{ 0:string, 1:string }> */
    public static function provideClassNames(): \Generator
    {
        $cases = [
            'Dagger' => '',
            'Dagger\\Class' => 'Class',
            'DaggerModule\\Class' => 'Class',
            'Dagger\\Tests\\Unit' => 'Tests\\Unit',
            'DaggerModule\\Tests\\Unit' => 'Tests\\Unit',
        ];

        foreach ($cases as $className => $classNameWithoutPrefix) {
            yield $className => [$className, $classNameWithoutPrefix];
        }
    }
}
