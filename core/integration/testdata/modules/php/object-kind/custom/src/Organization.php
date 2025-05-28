<?php

declare(strict_types=1);

namespace DaggerModule;

use Dagger\Attribute\{DaggerObject, ListOfType};
use Dagger\GitRepository;

#[DaggerObject] class Organization
{
    /**
     * @param list<GitRepository> $repositories
     * @param list<Account> $members
     */
    public function __construct(
        public string $url,
        #[ListOfType(GitRepository::class)]
        public array $repositories,
        #[ListOfType(Account::class)] public array $members,
    ) {}
}
