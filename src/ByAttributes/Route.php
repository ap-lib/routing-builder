<?php declare(strict_types=1);

namespace AP\Routing\Builder\ByAttributes;

use AP\Routing\Request\Method;
use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_METHOD)]
readonly class Route
{
    /**
     * @var array<Method>
     */
    public array $methods;

    /**
     * @param string $path
     * @param array|Method $method allow one or more
     * @param array|string $middleware
     */
    public function __construct(
        public string       $path,
        array|Method        $method = Method::GET,
        public array|string $middleware = [],
    )
    {
        if ($method instanceof Method) {
            $this->methods = [$method];
        } else {
            foreach ($method as $m) {
                if (!($m instanceof Method)) {
                    throw new \Error("all methods must be " . Method::class);
                }
            }
            $this->methods = $method;
        }
    }
}