<?php declare(strict_types=1);

namespace AP\Routing\Builder\ByAttributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
readonly class RouteGroup
{
    public function __construct(
        public string       $path,
        public array|string $middleware_append = [],
        public array|string $middleware_prepend = [],
    )
    {
    }
}