<?php declare(strict_types=1);

namespace Concept\Stack\Provider;

use Closure;
use Concept\Stack\Exceptions\ConceptStackException;
use Concept\Stack\Options\StackLayerOptions;
use League\Container\ServiceProvider\ServiceProviderInterface;

final class StackProviderRegistry
{
    /**
     * @var array<string, Closure(string, StackLayerOptions): ServiceProviderInterface>
     */
    private array $factories = [];

    /**
     * @param Closure(string, StackLayerOptions): ServiceProviderInterface $factory
     */
    public function set(string $layer, Closure $factory): self
    {
        $this->factories[$layer] = $factory;

        return $this;
    }

    /**
     * @param Closure(string, StackLayerOptions): ServiceProviderInterface $factory
     */
    public function replace(string $layer, Closure $factory): self
    {
        return $this->set($layer, $factory);
    }

    public function remove(string $layer): self
    {
        unset($this->factories[$layer]);

        return $this;
    }

    public function has(string $layer): bool
    {
        return isset($this->factories[$layer]);
    }

    /**
     * @param array<string, mixed> $options
     */
    public function make(string $layer, string $root, array $options = []): ServiceProviderInterface
    {
        if (!isset($this->factories[$layer])) {
            throw new ConceptStackException(sprintf('Stack layer factory is not registered: %s', $layer));
        }

        return ($this->factories[$layer])($root, new StackLayerOptions($options));
    }
}
