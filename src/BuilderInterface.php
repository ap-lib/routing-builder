<?php declare(strict_types=1);

namespace AP\Routing\Builder;

use AP\Routing\Routing\Routing\IndexInterface;

interface BuilderInterface
{
    public function modifyIndex(IndexInterface &$index): void;
}