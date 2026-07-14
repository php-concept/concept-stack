<?php declare(strict_types=1);

namespace Concept\Stack\Builder;

use Concept\Stack\Bricks\Casting\CastingBuilder;
use Concept\Stack\Bricks\Casting\CastingOptions;
use Concept\Stack\Bricks\Console\ConsoleBuilder;
use Concept\Stack\Bricks\Console\ConsoleOptions;
use Concept\Stack\Bricks\Database\DatabaseBuilder;
use Concept\Stack\Bricks\Database\DatabaseOptions;
use Concept\Stack\Bricks\Http\HttpBuilder;
use Concept\Stack\Bricks\Http\HttpOptions;
use Concept\Stack\Bricks\Logging\LoggingBuilder;
use Concept\Stack\Bricks\Logging\LoggingOptions;
use Concept\Stack\Bricks\Masking\MaskingBuilder;
use Concept\Stack\Bricks\Masking\MaskingOptions;
use Concept\Stack\Bricks\Session\SessionBuilder;
use Concept\Stack\Bricks\Session\SessionOptions;
use Concept\Stack\Bricks\Validation\ValidationBuilder;
use Concept\Stack\Bricks\Validation\ValidationOptions;
use Concept\Stack\Bricks\View\ViewBuilder;
use Concept\Stack\Bricks\View\ViewOptions;
use Concept\Stack\Capability\CapabilityRegistry;
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

    public function withMasking(): MaskingBuilder
    {
        return new MaskingBuilder($this, new MaskingOptions());
    }

    public function withLogging(): LoggingBuilder
    {
        return new LoggingBuilder($this, new LoggingOptions());
    }

    public function withCasting(): CastingBuilder
    {
        return new CastingBuilder($this, new CastingOptions());
    }

    public function withDatabase(): DatabaseBuilder
    {
        return new DatabaseBuilder($this, new DatabaseOptions());
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

    public function withSession(): SessionBuilder
    {
        return new SessionBuilder($this, new SessionOptions());
    }

    public function withView(): ViewBuilder
    {
        return new ViewBuilder($this, new ViewOptions());
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
