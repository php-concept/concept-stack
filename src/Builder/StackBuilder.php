<?php declare(strict_types=1);

namespace Concept\Stack\Builder;

use Concept\Stack\Capability\CapabilityRegistry;
use Concept\Stack\Contract\BindableBrickInterface;
use Concept\Stack\Contract\BrickInterface;
use League\Container\ServiceProvider\ServiceProviderInterface;

/**
 * Thin stack assembler: register bricks (+ optional raw providers), resolve order, emit providers.
 *
 * First-party capabilities live in php-concept/brick-* packages — use add(new HttpBrick()), not addHttp().
 */
final class StackBuilder
{
    /** @var array<string, BrickInterface> */
    private array $bricks = [];

    /** @var list<ServiceProviderInterface> */
    private array $additionalProviders = [];

    private readonly CapabilityRegistry $capabilities;

    public function __construct()
    {
        $this->capabilities = new CapabilityRegistry();
    }

    /**
     * Register a brick and return it for fluent configuration.
     *
     * @template T of BrickInterface
     * @param T $brick
     * @return T
     */
    public function add(BrickInterface $brick): BrickInterface
    {
        $this->capabilities->register($brick->name(), $brick->requires());
        $this->bricks[$brick->name()] = $brick;

        if ($brick instanceof BindableBrickInterface) {
            $brick->bind($this);
        }

        return $brick;
    }

    /**
     * @deprecated Use add() — returns the brick for fluent builder() calls.
     */
    public function addCustom(BrickInterface $brick): self
    {
        $this->add($brick);

        return $this;
    }

    /**
     * Escape hatch for application-specific providers that are not stack capabilities.
     */
    public function addProvider(ServiceProviderInterface $provider): self
    {
        $this->additionalProviders[] = $provider;

        return $this;
    }

    public function require(string $capability, string $dependency): void
    {
        $this->capabilities->require($capability, $dependency);
    }

    public function hasCapability(string $name): bool
    {
        return $this->capabilities->has($name);
    }

    /**
     * @return list<ServiceProviderInterface>
     */
    public function providers(): array
    {
        $providers = [];
        foreach ($this->capabilities->orderedNames() as $name) {
            array_push($providers, ...$this->bricks[$name]->providers());
        }

        return [...$providers, ...$this->additionalProviders];
    }
}
