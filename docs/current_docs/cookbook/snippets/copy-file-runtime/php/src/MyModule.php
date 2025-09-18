<?php

declare(strict_types=1);

namespace DaggerModule;

use Dagger\Attribute\{DaggerObject, DaggerFunction, Doc};
use Dagger\File;

use function Dagger\dag;

#[DaggerObject]
class MyModule
{
    #[DaggerFunction]
    #[Doc('Copy a file to the Dagger module runtime container for custom processing')]
    public function copyFile(File $source) {
        $source->export('foo.txt');
        // your custom logic here
        // for example, read and print the file in the Dagger Engine container
        echo file_get_contents('foo.txt');
    }
}
