<?php declare(strict_types=1);

namespace Concept\Stack\Contract;

use Concept\Stack\Builder\StackBuilder;

/**
 * Brick that needs StackBuilder after registration (dynamic require() from with*()).
 */
interface BindableBrickInterface extends BrickInterface
{
    public function bind(StackBuilder $stack): void;
}
