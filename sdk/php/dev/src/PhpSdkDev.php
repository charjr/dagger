<?php

declare(strict_types=1);

namespace DaggerModule;

use Dagger\Attribute\DaggerFunction;
use Dagger\Attribute\DaggerObject;
use Dagger\Attribute\DefaultPath;
use Dagger\Attribute\Doc;
use Dagger\Container;
use Dagger\Directory;
use Dagger\ReturnType;
use GraphQL\Exception\QueryError;

use function Dagger\dag;

#[DaggerObject]
#[Doc('The PHP SDK\'s development module.')]
final class PhpSdkDev
{
    private const SDK_ROOT = '/src/sdk/php';

    #[DaggerFunction]
    public function __construct(
        #[DefaultPath('..')]
        #[Doc('The PHP SDK source directory.')]
        private Directory $source,
        private ?Container $container = null,
    ) {
        if (is_null($this->container)) {
            $this->container = dag()
                ->container()
                ->from('php:8.3-cli-alpine')
                ->withFile('/usr/bin/composer', dag()
                    ->container()
                    ->from('composer:2')
                    ->file('/usr/bin/composer'))
                ->withMountedCache('/root/.composer', dag()
                    ->cacheVolume('composer-php:8.3-cli-alpine'))
                ->withEnvVariable('COMPOSER_HOME', '/root/.composer')
                ->withEnvVariable('COMPOSER_NO_INTERACTION', '1')
                ->withEnvVariable('COMPOSER_ALLOW_SUPERUSER', '1');
        }

        $this->container = $this->container
            ->withMountedDirectory(self::SDK_ROOT, $this->source)
            ->withWorkdir(self::SDK_ROOT)
            ->withExec(['composer', 'install'])
            ->withEnvVariable('PATH', './vendor/bin:$PATH', expand: true);
    }

    #[DaggerFunction]
    #[Doc('Run tests in source directory')]
    public function test(
        #[Doc('Only run tests in the given group')]
        ?string $group = null,
    ): Container {
        return $this->container->withExec(
            is_null($group) ? ['phpunit'] : ['phpunit', "--group=$group"],
        );
    }

    #[DaggerFunction]
    #[Doc('Check for linting errors')]
    public function lint(): Container
    {
        return $this->container->withExec([
            'php-cs-fixer',
            'check',
            '--show-progress=none', // Clogs up output in Dagger traces
            '--using-cache=no', // Dagger already caches identical calls
        ]);
    }

    #[DaggerFunction]
    #[Doc('format source files')]
    public function format(): Directory
    {
        $result = $this->container->withExec([
            'php-cs-fixer',
            'fix',
            '--show-progress=none', // Clogs up output in Dagger traces
            '--using-cache=no', // Dagger already caches identical calls
        ]);

        $original = $this->container->directory(self::SDK_ROOT);

        return $original->diff($result->directory(self::SDK_ROOT));
    }

    #[DaggerFunction]
    #[Doc('Base container to run CI on the PHP SDK source files')]
    public function base(): Container
    {
        return $this->container;
    }
}
