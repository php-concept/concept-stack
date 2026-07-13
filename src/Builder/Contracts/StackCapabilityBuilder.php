<?php declare(strict_types=1);

namespace Concept\Stack\Builder\Contracts;

use Concept\Stack\Builder\StackBuilder;

interface StackCapabilityBuilder
{
    public function end(): StackBuilder;
}
