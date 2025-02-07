<?php declare(strict_types=1);

namespace AP\Routing\Builder\Tests\Handlers;

use AP\Routing\Builder\ByAttributes\Route;

class Main
{
    #[Route(path: "/")]
    public static function root()
    {

    }
}
