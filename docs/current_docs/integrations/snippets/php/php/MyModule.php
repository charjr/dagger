<?php

declare(strict_types=1);

use Dagger\{Directory, Container, Secret};
use Dagger\Attribute\{DaggerObject, DaggerFunction};

use function Dagger\dag;

#[DaggerObject]
class MyModule
{
    #[DaggerFunction('return container image with application source code and dependencies')]
    public function build(Directory $source): Container
    {
        return dag()->container()
            ->from('php:8.2')
            ->withExec(['apt-get', 'update'])
            ->withExec(['apt-get', 'install', '--yes', 'git-core', 'zip', 'curl'])
            ->withFile('/usr/bin/composer', dag()
                ->container()
                ->from('composer:2')
                ->file('/usr/bin/composer'))
            ->withDirectory('/var/www', $source->withoutDirectory('dagger'))
            ->withWorkdir('/var/www')
            ->withExec(['chmod', '-R', '755', '/var/www'])
            ->withEnvVariable('PATH', './vendor/bin:$PATH', true)
            ->withExec(['composer', 'install']);
    }

    #[DaggerFunction('return result of unit tests')]
    public function test(Directory $source): string
    {
        return $this->build($source)->withExec(['phpunit'])->stdout();
    }

    #[DaggerFunction('Return address of published container image')]
    public function publish(
        Directory $source,
        string $version,
        string $registryAddress,
        string $registerUsername,
        Secret $registryPassword,
        string $imageName,
    ): string
    {
        return $this->build($source)
            ->withLabel('org.opencontainers.image.title', 'Laravel with Dagger')
            ->withLabel('org.opencontainers.image.version', $version)
            ->withEntrypoint(['php', '-S', '0.0.0.0:8080', '-t', 'public'])
            ->withExposedPort(8080)
            ->withRegistryAuth($registryAddress, $registerUsername, $registryPassword)
            ->publish("$registryAddress/$registerUsername/$imageName");
    }
}
