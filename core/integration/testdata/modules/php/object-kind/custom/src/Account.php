<?php

declare(strict_types=1);

namespace DaggerModule;

use Dagger\Attribute\DaggerObject;

#[DaggerObject] class Account
{
    public function __construct(
        public string $username,
        public string $email,
    ) {}
}
