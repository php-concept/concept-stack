<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Components;

use Concept\Extensions\Components\Contracts\ComponentInterface;
use Concept\Stack\Builder\StackBuilder;
use Concept\Stack\Capability\Capability;

final class ComponentsBuilder
{
    public function __construct(
        private readonly StackBuilder $parent,
        private readonly ComponentsOptions $options,
    ) {}

    /**
     * @param list<class-string<ComponentInterface>> $componentClasses
     */
    public function setClasses(array $componentClasses): self
    {
        $this->options->setComponentClasses($componentClasses);

        return $this;
    }

    public function withDatabase(): self
    {
        $this->options->setDatabase(true);
        $this->parent->require(Capability::COMPONENTS, Capability::DATABASE);

        return $this;
    }

    public function withConsole(): self
    {
        $this->options->setConsole(true);
        $this->parent->require(Capability::COMPONENTS, Capability::CONSOLE);

        return $this;
    }

    public function withHttp(): self
    {
        $this->options->setHttp(true);
        $this->parent->require(Capability::COMPONENTS, Capability::HTTP);

        return $this;
    }

    public function withView(): self
    {
        $this->options->setView(true);
        $this->parent->require(Capability::COMPONENTS, Capability::VIEW);

        return $this;
    }
}
