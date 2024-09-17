<?php

declare(strict_types=1);

namespace Dagger\Service;

final class RemovesNamespacePrefix
{
    // NormalisesClassName
    // ::getShortName
    // ::removeNamespacePrefix

    /** @param class-string $name */
    public function __invoke(string $name): string
    {
        $result = explode('\\', $name);
        array_shift($result);
        return implode('\\', $result);
    }
}
