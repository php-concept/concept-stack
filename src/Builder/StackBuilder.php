<?php declare(strict_types=1);

namespace Concept\Stack\Builder;

use Concept\Stack\Capability\CapabilityRegistry;
use Concept\Stack\Options\CastingOptions;
use Concept\Stack\Options\ConsoleOptions;
use Concept\Stack\Options\HttpOptions;
use Concept\Stack\Options\ValidationOptions;
use League\Container\ServiceProvider\ServiceProviderInterface;

final class StackBuilder
{
    /** @var list<ServiceProviderInterface> */
    private array $providers = [];

    private readonly CapabilityRegistry $capabilities;

    public function __construct()
    {
        $this->capabilities = new CapabilityRegistry();
    }

    public function withCasting(): CastingBuilder
    {
        return new CastingBuilder($this, new CastingOptions());
    }

    public function withValidation(): ValidationBuilder
    {
        return new ValidationBuilder($this, new ValidationOptions());
    }

    public function withConsole(): ConsoleBuilder
    {
        return new ConsoleBuilder($this, new ConsoleOptions());
    }

    public function withHttp(): HttpBuilder
    {
        return new HttpBuilder($this, new HttpOptions());
    }

    /**
     * Registers a capability provider together with the capabilities it depends on.
     *
     * @param list<string> $requires
     */
    public function registerCapability(
        string $name,
        ServiceProviderInterface $provider,
        array $requires = [],
    ): void {
        $this->capabilities->register($name, $requires);
        $this->providers[] = $provider;
    }

    public function hasCapability(string $name): bool
    {
        return $this->capabilities->has($name);
    }

    /**
     * Escape hatch for application-specific providers that are not stack capabilities.
     */
    public function addProvider(ServiceProviderInterface $provider): self
    {
        $this->providers[] = $provider;

        return $this;
    }

    /**
     * @return list<ServiceProviderInterface>
     */
    public function providers(): array
    {
        $this->capabilities->assertDependencies();

        return $this->providers;
    }
}
