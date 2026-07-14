<?php declare(strict_types=1);

namespace Concept\Stack\Builder;

use Concept\Stack\Bricks\Casting\CastingBuilder;
use Concept\Stack\Bricks\Casting\CastingOptions;
use Concept\Stack\Bricks\Casting\CastingStackProvider;
use Concept\Stack\Bricks\Console\ConsoleBuilder;
use Concept\Stack\Bricks\Console\ConsoleOptions;
use Concept\Stack\Bricks\Console\ConsoleStackProvider;
use Concept\Stack\Bricks\Database\DatabaseBuilder;
use Concept\Stack\Bricks\Database\DatabaseOptions;
use Concept\Stack\Bricks\Database\DatabaseStackProvider;
use Concept\Stack\Bricks\Http\HttpBuilder;
use Concept\Stack\Bricks\Http\HttpOptions;
use Concept\Stack\Bricks\Http\HttpStackProvider;
use Concept\Stack\Bricks\Logging\LoggingBuilder;
use Concept\Stack\Bricks\Logging\LoggingOptions;
use Concept\Stack\Bricks\Logging\LoggingStackProvider;
use Concept\Stack\Bricks\Masking\MaskingBuilder;
use Concept\Stack\Bricks\Masking\MaskingOptions;
use Concept\Stack\Bricks\Masking\MaskingStackProvider;
use Concept\Stack\Bricks\Session\SessionBuilder;
use Concept\Stack\Bricks\Session\SessionOptions;
use Concept\Stack\Bricks\Session\SessionStackProvider;
use Concept\Stack\Bricks\Validation\ValidationBuilder;
use Concept\Stack\Bricks\Validation\ValidationOptions;
use Concept\Stack\Bricks\Validation\ValidationStackProvider;
use Concept\Stack\Bricks\View\ViewBuilder;
use Concept\Stack\Bricks\View\ViewOptions;
use Concept\Stack\Bricks\View\ViewStackProvider;
use Concept\Stack\Capability\Capability;
use Concept\Stack\Capability\CapabilityRegistry;
use League\Container\ServiceProvider\ServiceProviderInterface;

final class StackBuilder
{
    /** @var list<ServiceProviderInterface> */
    private array $providers = [];

    /** @var list<callable(): void> */
    private array $optionValidators = [];

    private readonly CapabilityRegistry $capabilities;

    public function __construct()
    {
        $this->capabilities = new CapabilityRegistry();
    }

    public function withMasking(): MaskingBuilder
    {
        $options = new MaskingOptions();
        $this->registerCapability(Capability::MASKING, new MaskingStackProvider($options));

        return new MaskingBuilder($options);
    }

    public function withLogging(): LoggingBuilder
    {
        $options = new LoggingOptions();
        $this->registerCapability(
            Capability::LOGGING,
            new LoggingStackProvider($options),
            assertValid: static fn() => $options->assertValid(),
        );

        return new LoggingBuilder($this, $options);
    }

    public function withCasting(): CastingBuilder
    {
        $options = new CastingOptions();
        $this->registerCapability(Capability::CASTING, new CastingStackProvider($options));

        return new CastingBuilder($options);
    }

    public function withDatabase(): DatabaseBuilder
    {
        $options = new DatabaseOptions();
        $this->registerCapability(
            Capability::DATABASE,
            new DatabaseStackProvider($options),
            assertValid: static fn() => $options->assertValid(),
        );

        return new DatabaseBuilder($this, $options);
    }

    public function withValidation(): ValidationBuilder
    {
        $options = new ValidationOptions();
        $this->registerCapability(Capability::VALIDATION, new ValidationStackProvider($options));

        return new ValidationBuilder($options);
    }

    public function withConsole(): ConsoleBuilder
    {
        $options = new ConsoleOptions();
        $this->registerCapability(Capability::CONSOLE, new ConsoleStackProvider($options));

        return new ConsoleBuilder($options);
    }

    public function withHttp(): HttpBuilder
    {
        $options = new HttpOptions();
        $this->registerCapability(
            Capability::HTTP,
            new HttpStackProvider($options),
            assertValid: static fn() => $options->assertValid(),
        );

        return new HttpBuilder($this, $options);
    }

    public function withSession(): SessionBuilder
    {
        $options = new SessionOptions();
        $this->registerCapability(Capability::SESSION, new SessionStackProvider($options));

        return new SessionBuilder($options);
    }

    public function withView(): ViewBuilder
    {
        $options = new ViewOptions();
        $this->registerCapability(
            Capability::VIEW,
            new ViewStackProvider($options),
            requires: [Capability::HTTP],
            assertValid: static fn() => $options->assertValid(),
        );

        return new ViewBuilder($options);
    }

    /**
     * @param list<string> $requires
     * @param callable(): void|null $assertValid
     */
    public function registerCapability(
        string $name,
        ServiceProviderInterface $provider,
        array $requires = [],
        ?callable $assertValid = null,
    ): void {
        $this->capabilities->register($name, $requires);
        $this->providers[] = $provider;

        if ($assertValid !== null) {
            $this->optionValidators[] = $assertValid;
        }
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
        foreach ($this->optionValidators as $assertValid) {
            $assertValid();
        }

        $this->capabilities->assertDependencies();

        return $this->providers;
    }
}
