<?php declare(strict_types=1);

namespace AP\Routing\Builder\ByAttributes;

use AP\Routing\Request\Method;
use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_METHOD)]
readonly class RoutePost extends Route
{
    /**
     * @var array<Method>
     */
    public array $methods;

    /**
     * @param string $path
     * @param array|string $middleware
     */
    public function __construct(
        string       $path,
        array|string $middleware = [],
    )
    {
        parent::__construct($path, Method::POST, $middleware);
    }
}