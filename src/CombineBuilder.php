<?php declare(strict_types=1);

namespace AP\Routing\Builder;

use AP\Routing\Routing\Routing\IndexInterface;
use InvalidArgumentException;

class CombineBuilder implements BuilderInterface
{
    /**
     * @param array<BuilderInterface> $builders
     */
    public function __construct(public array $builders)
    {
        foreach ($this->builders as $builder) {
            if (!($builder instanceof BuilderInterface)) {
                throw new InvalidArgumentException("all builders must implement `BuilderInterface`");
            }
        }
    }

    public function modifyIndex(IndexInterface &$index): void
    {
        foreach ($this->builders as $builder) {
            $builder->modifyIndex($index);
        }
    }
}