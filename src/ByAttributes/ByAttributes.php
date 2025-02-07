<?php declare(strict_types=1);

namespace AP\Routing\Builder\ByAttributes;

use AP\DirectoryClassFinder\DirectoryClassFinderComposerPSR4;
use AP\Logger\Log;
use AP\Routing\Builder\BuilderInterface;
use AP\Routing\Routing\Endpoint;
use AP\Routing\Routing\Routing\IndexInterface;
use Error;
use Generator;
use InvalidArgumentException;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class ByAttributes implements BuilderInterface
{
    public const string LOG_NAME = 'ap:by_attributes_routing_builder';

    public function __construct(
        protected string $directory,
        protected bool   $recursive = true,
    )
    {
    }

    /**
     * @return Generator<class-string>
     */
    public function getClasses(): Generator
    {
        return (new DirectoryClassFinderComposerPSR4(
            include_vendor_classes: str_starts_with(
                $this->directory,
                DirectoryClassFinderComposerPSR4::getVendorDirectory()
            ),
            recheck_founded_by_psr4_name: true,
            recheck_founder_on_classmap: true, // because anyway, this code will load all files to work with attributes
        ))->getClasses(
            $this->directory,
            $this->recursive
        );
    }


    /**
     * @param class-string $class
     * @param IndexInterface $index
     * @return void
     * @throws ReflectionException
     */
    public function modifyIndexForOneClass(string $class, IndexInterface &$index): void
    {
        $classRef           = new ReflectionClass($class);
        $classRouteGroupRef = $classRef->getAttributes(RouteGroup::class, ReflectionAttribute::IS_INSTANCEOF);
        if (count($classRouteGroupRef) > 1) {
            throw new Error("Attribute `" . RouteGroup::class . "` must not be repeated on class `$class`");
        }
        $routeGroup = isset($classRouteGroupRef[0]) ? $classRouteGroupRef[0]->newInstance() : null;
        $methodsRef = $classRef->getMethods();

        foreach ($methodsRef as $methodRef) {
            $routesRef = $methodRef->getAttributes(Route::class, ReflectionAttribute::IS_INSTANCEOF);
            if (count($routesRef)) {
                $handler = "$class::" . $methodRef->getName();
                // post validate method
                if (!$methodRef->isStatic()) throw new Error("handler `$handler` must be static");
                if (!$methodRef->isPublic()) throw new Error("handler `$handler` must be public");
            }
            foreach ($routesRef as $routeRef) {
                $this->modifyIndexForOneRoute(
                    $index,
                    $classRef,
                    $methodRef,
                    $routeRef->newInstance(),
                    $routeGroup
                );
            }
        }
    }

    protected function modifyIndexForOneRoute(
        IndexInterface   &$index,
        ReflectionClass  $classRef,
        ReflectionMethod $methodRef,
        Route            $route,
        ?RouteGroup      $routeGroup = null,
    )
    {
        $class = $classRef->getName();

        if ($routeGroup instanceof RouteGroup) {
            $mvs = [
                $routeGroup->middleware_prepend,
                $route->middleware,
                $routeGroup->middleware_append,
            ];

            $middleware = [];
            foreach ($mvs as $mv) {
                $mv = self::modifyMiddlewares($class, $mv);
                if (count($mv)) {
                    $middleware = array_merge($middleware, $mv);
                }
            }
        } else {
            $middleware = self::modifyMiddlewares($class, $route->middleware);
        }

        foreach ($route->methods as $method) {
            $index->addEndpoint(
                $method,
                $routeGroup instanceof RouteGroup
                    ? $routeGroup->path . $route->path
                    : $route->path,
                new Endpoint(
                    $class . "::" . $methodRef->getName(),
                    $middleware
                )
            );
        }
    }

    public function modifyIndex(IndexInterface &$index): void
    {
        foreach ($this->getClasses() as $class) {
            Log::debug(
                "classes found",
                $this->getClasses(),
                "ap:"
            );
            $this->modifyIndexForOneClass($class, $index);
        }
    }

    /////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @param class-string $class
     * @param string|array $middleware
     * @return callable-string
     * @throws InvalidArgumentException
     */
    public static function modifyMiddlewaresElement(string $class, string|array $middleware): string
    {
        $callable_name = "";
        if (
            is_array($middleware)
            && count($middleware) == 2
            && isset($middleware[0], $middleware[1])
            && is_callable($middleware, callable_name: $callable_name)
        ) {
            return $callable_name;
        }

        if (is_string($middleware)) {
            if (is_callable([$class, $middleware], callable_name: $callable_name)) {
                return $callable_name;
            } elseif (is_callable($middleware, callable_name: $callable_name)) {
                return $callable_name;
            }
        }

        throw new InvalidArgumentException();
    }

    /**
     * @param class-string $class
     * @param string|array $middleware
     * @return array<callable-string>
     */
    public static function modifyMiddlewares(string $class, string|array $middleware): array
    {
        try {
            return [self::modifyMiddlewaresElement($class, $middleware)];
        } catch (InvalidArgumentException $e) {
        }

        if (is_array($middleware)) {
            try {
                $res = [];
                foreach ($middleware as $m) {
                    $res[] = self::modifyMiddlewaresElement($class, $m);
                }
                return $res;
            } catch (InvalidArgumentException $e) {
            }
        }

        throw new InvalidArgumentException(
            "Middleware must be callable, a string representing a method name of the current class, or an array of these"
        );
    }
}