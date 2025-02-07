<?php declare(strict_types=1);

namespace AP\Routing\Builder\Tests;

use AP\Routing\Builder\ByAttributes\ByAttributes;
use AP\Routing\Routing\Routing\Hashmap\HashmapIndex;
use PHPUnit\Framework\TestCase;

final class MainTest extends TestCase
{
    public function testFirst(): void
    {
        $index = new HashmapIndex();

        $builder = new ByAttributes(__DIR__ . "/Handlers");
        $builder->modifyIndex($index);


        $this->assertEquals(
            [
                'GET'    => [
                    '/'      => 'AP\\Routing\\Builder\\Tests\\Handlers\\Main::root',
                    '/users' => 'AP\\Routing\\Builder\\Tests\\Handlers\\Users::list',
                ],
                'POST'   => [
                    '/users/create' => 'AP\\Routing\\Builder\\Tests\\Handlers\\Users::create',
                ],
                'PUT'    => [
                    '/users/update' => 'AP\\Routing\\Builder\\Tests\\Handlers\\Users::update',
                ],
                'DELETE' => [
                    '/users/delete' => 'AP\\Routing\\Builder\\Tests\\Handlers\\Users::delete',
                ],
            ],
            $index->make()
        );
    }
}
