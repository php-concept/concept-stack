<?php declare(strict_types=1);

namespace Concept\Stack\Support;

use Concept\Stack\Builder\StackBuilder;
use RuntimeException;

trait BindsToStack
{
    private const string ERR_NOT_BOUND = 'Brick must be registered via StackBuilder::add() before stack-aware methods.';

    private ?StackBuilder $stack = null;

    public function bind(StackBuilder $stack): void
    {
        $this->stack = $stack;
    }

    protected function stack(): StackBuilder
    {
        if ($this->stack === null) {
            throw new RuntimeException(self::ERR_NOT_BOUND);
        }

        return $this->stack;
    }
}
