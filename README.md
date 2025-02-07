# AP\Routing\Builder

[![MIT License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

A helper repository for managing routing indexes: https://github.com/ap-lib/routing
This package allows you to define routes using attributes directly in your handlers, making the development process easier.

## Installation

```bash
composer require ap-lib/routing-builder
```

## Features

- Help to define [routing index](https://github.com/ap-lib/routing) using attributes
- Group routes easily with RouteGroup

## Requirements

- PHP 8.3 or higher

## Getting started

### Base controller

```php
class Main
{
    #[Route(path: "/")]
    public static function root(): string
    {
        return "hello world";
    }
}
```

### Group routes 
```php
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
            ["id" => 123456],
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
        return new Response(code: 204);
    }
}
```

### Preparing an Index for All Classes in a Directory
For more details on routing and indexes, see: https://github.com/ap-lib/routing

```php
$index = new HashmapIndex();

$builder = new ByAttributes(__DIR__ . "/Handlers");
$builder->modifyIndex($index);

```