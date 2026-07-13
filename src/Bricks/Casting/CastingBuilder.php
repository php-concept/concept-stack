<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Casting;

use Concept\Stack\Builder\Contracts\StackCapabilityBuilder;
use Concept\Stack\Builder\StackBuilder;
use Concept\Stack\Capability\Capability;

final class CastingBuilder implements StackCapabilityBuilder
{
    public function __construct(
        private readonly StackBuilder $parent,
        private readonly CastingOptions $options,
    ) {}

    public function cacheDir(string $cacheDirectory): self
    {
        $this->options->setCacheDirectory($cacheDirectory);

        return $this;
    }

    /**
     * @param list<class-string> $transformerClasses
     */
    public function transformers(array $transformerClasses): self
    {
        $this->options->setTransformerClasses($transformerClasses);

        return $this;
    }

    public function debug(bool $debug = true): self
    {
        $this->options->setDebug($debug);

        return $this;
    }

    public function end(): StackBuilder
    {
        $this->parent->registerCapability(
            Capability::CASTING,
            new CastingStackProvider($this->options),
        );

        return $this->parent;
    }
}
