<?php

declare(strict_types=1);

namespace DaggerModule;

use Dagger\Attribute\{DaggerFunction, DaggerObject, ReturnsListOfType};

use function Dagger\dag;

#[DaggerObject] class Custom
{
    #[DaggerFunction] public function daggerOrganization(): Organization
    {
        $url = 'https://github.com/dagger';
        return new Organization($url, [dag()->git("$url/dagger")], [
            new Account('jane', 'jane@example.com'),
            new Account('john', 'john@example.com'),
        ]);
    }

    #[DaggerFunction, ReturnsListOfType('string')]
    public function getUrlsOfMembers(Organization $arg): array
    {
        return array_map(fn ($m) => $m->url, $arg->members);
    }
}
