<?php declare(strict_types=1);

namespace AP\Routing\Builder\Tests\Handlers;

use AP\Routing\Builder\ByAttributes\Route;
use AP\Routing\Builder\ByAttributes\RouteGroup;
use AP\Routing\Request\Method;
use AP\Routing\Response\Json;
use AP\Routing\Response\Response;

#[RouteGroup(path: "/users")]
class Users
{
    #[Route(path: "")]
    public static function list(): array
    {
        return [];
    }

    #[Route(path: "/create", method: Method::POST)]
    public static function create(): Json
    {
        return new Json(
            ["id" => 123],
            201
        );
    }

    #[Route(path: "/update", method: Method::PUT)]
    public static function update(): Response
    {
        return new Response(code: 204);
    }

    #[Route(path: "/delete", method: Method::DELETE)]
    public static function delete(): Response
    {
        return new Response(code: 204); // Success, no response
    }
}
