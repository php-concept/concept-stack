<?php declare(strict_types=1);

namespace Concept\Stack;

use Concept\Stack\Builder\StackBuilder;

final class ConceptStack
{
    public static function create(): StackBuilder
    {
        return new StackBuilder();
    }
}
