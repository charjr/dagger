<?php

declare(strict_types=1);

namespace DaggerModule;

use Dagger\Attribute\DaggerFunction;
use Dagger\Attribute\DaggerObject;
use Dagger\Attribute\Doc;
use Dagger\Container;
use Dagger\Directory;

use function Dagger\dag;

#[DaggerObject]
#[Doc("The PHP SDK's development module.")]
final class PhpSdkDev
{
    #[DaggerFunction]
    #[Doc('Run tests from source directory')]
    public function test(
        #[Doc('Run tests from the given source directory')]
        Directory $source,
        #[Doc('Only run tests in the given group')]
        ?string $group = null,
    ): Container {
        return $this->base($source)->withExec(
            is_null($group) ? ['phpunit'] : ['phpunit', "--group=$group"],
            experimentalPrivilegedNesting: true,
        );
    }

    #[DaggerFunction]
    #[Doc('Run linter in source directory')]
    public function lint(Directory $source): Container
    {
        return $this->base($source)->withExec(['phpcs']);
    }

    private function base(Directory $source): Container
    {
        return dag()
            ->container()
            ->from('php:8.3-cli-alpine')
            ->withFile('/usr/bin/composer', dag()
                ->container()
                ->from('composer:2')
                ->file('/usr/bin/composer'))
            ->withMountedCache('/root/.composer', dag()
                ->cacheVolume('composer-php:8.3-cli-alpine'))
            ->withEnvVariable('COMPOSER_HOME', '/root/.composer')
            ->withEnvVariable('COMPOSER_ALLOW_SUPERUSER', '1')
            ->withMountedDirectory('/src/sdk/php', $source)
            ->withWorkdir('/src/sdk/php')
            ->withExec(['composer', 'install'])
            ->withEnvVariable('PATH', './vendor/bin:$PATH', expand: true);
    }
}
