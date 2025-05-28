<?php

declare(strict_types=1);

namespace Dagger\Tests\Unit\Fixture;

use JMS\Serializer\Annotation\Type;

final class CustomObject
{
    public function __construct(
        #[Type('list<string>')]
        public array $listArg,
    ) {}
}
