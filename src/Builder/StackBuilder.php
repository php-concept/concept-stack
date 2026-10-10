<?php declare(strict_types=1);

namespace Concept\Stack\Builder;

use Concept\Stack\Bricks\Casting\CastingBuilder;
use Concept\Stack\Bricks\Casting\CastingOptions;
use Concept\Stack\Bricks\Casting\CastingStackProvider;
use Concept\Stack\Bricks\Console\ConsoleBuilder;
use Concept\Stack\Bricks\Console\ConsoleOptions;
use Concept\Stack\Bricks\Console\ConsoleStackProvider;
use Concept\Stack\Bricks\Components\ComponentsBuilder;
use Concept\Stack\Bricks\Components\ComponentsOptions;
use Concept\Stack\Bricks\Components\ComponentsStackProvider;
use Concept\Stack\Bricks\Database\DatabaseBuilder;
use Concept\Stack\Bricks\Database\DatabaseOptions;
use Concept\Stack\Bricks\Database\DatabaseStackProvider;
use Concept\Stack\Bricks\ErrorHandling\ErrorHandlingBuilder;
use Concept\Stack\Bricks\ErrorHandling\ErrorHandlingOptions;
use Concept\Stack\Bricks\ErrorHandling\ErrorHandlingStackProvider;
use Concept\Stack\Bricks\Events\EventsBuilder;
use Concept\Stack\Bricks\Events\EventsOptions;
use Concept\Stack\Bricks\Events\EventsStackProvider;
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
use Concept\Stack\Bricks\Telemetry\TelemetryBuilder;
use Concept\Stack\Bricks\Telemetry\TelemetryOptions;
use Concept\Stack\Bricks\Telemetry\TelemetryStackProvider;
use Concept\Stack\Bricks\Validation\ValidationBuilder;
use Concept\Stack\Bricks\Validation\ValidationOptions;
use Concept\Stack\Bricks\Validation\ValidationStackProvider;
use Concept\Stack\Bricks\View\ViewBuilder;
use Concept\Stack\Bricks\View\ViewOptions;
use Concept\Stack\Bricks\View\ViewStackProvider;
use Concept\Stack\Capability\Capability;
use Concept\Stack\Capability\CapabilityRegistry;
use Concept\Stack\Contract\BrickInterface;
use League\Container\ServiceProvider\ServiceProviderInterface;

final class StackBuilder
{
    /** @var array<string, ServiceProviderInterface> */
    private array $capabilityProviders = [];

    /** @var array<string, BrickInterface> */
    private array $customBricks = [];

    /** @var list<ServiceProviderInterface> */
    private array $additionalProviders = [];

    /** @var list<callable(): void> */
    private array $optionValidators = [];

    private readonly CapabilityRegistry $capabilities;

    public function __construct()
    {
        $this->capabilities = new CapabilityRegistry();
    }

    public function addMasking(): MaskingBuilder
    {
        $options = new MaskingOptions();
        $this->registerCapability(Capability::MASKING, new MaskingStackProvider($options));

        return new MaskingBuilder($options);
    }

    public function addLogging(): LoggingBuilder
    {
        $options = new LoggingOptions();
        $this->registerCapability(
            Capability::LOGGING,
            new LoggingStackProvider($options),
            assertValid: static fn() => $options->assertValid(),
        );

        return new LoggingBuilder($this, $options);
    }

    public function addEvents(): EventsBuilder
    {
        $options = new EventsOptions();
        $this->registerCapability(Capability::EVENTS, new EventsStackProvider($options));

        return new EventsBuilder($options);
    }

    public function addTelemetry(): TelemetryBuilder
    {
        $options = new TelemetryOptions();
        $this->registerCapability(
            Capability::TELEMETRY,
            new TelemetryStackProvider($options),
            assertValid: static fn() => $options->assertValid(),
        );

        return new TelemetryBuilder($this, $options);
    }

    public function addErrorHandling(): ErrorHandlingBuilder
    {
        $options = new ErrorHandlingOptions();
        $this->registerCapability(
            Capability::ERROR_HANDLING,
            new ErrorHandlingStackProvider($options),
            assertValid: static fn() => $options->assertValid(),
        );

        return new ErrorHandlingBuilder($this, $options);
    }

    public function addCasting(): CastingBuilder
    {
        $options = new CastingOptions();
        $this->registerCapability(Capability::CASTING, new CastingStackProvider($options));

        return new CastingBuilder($options);
    }

    public function addDatabase(): DatabaseBuilder
    {
        $options = new DatabaseOptions();
        $this->registerCapability(
            Capability::DATABASE,
            new DatabaseStackProvider($options),
            assertValid: static fn() => $options->assertValid(),
        );

        return new DatabaseBuilder($this, $options);
    }

    public function addValidation(): ValidationBuilder
    {
        $options = new ValidationOptions();
        $this->registerCapability(Capability::VALIDATION, new ValidationStackProvider($options));

        return new ValidationBuilder($this, $options);
    }

    public function addConsole(): ConsoleBuilder
    {
        $options = new ConsoleOptions();
        $this->registerCapability(Capability::CONSOLE, new ConsoleStackProvider($options));

        return new ConsoleBuilder($options);
    }

    public function addComponents(): ComponentsBuilder
    {
        $options = new ComponentsOptions();
        $this->registerCapability(Capability::COMPONENTS, new ComponentsStackProvider($options));

        return new ComponentsBuilder($this, $options);
    }

    public function addHttp(): HttpBuilder
    {
        $options = new HttpOptions();
        $this->registerCapability(
            Capability::HTTP,
            new HttpStackProvider($options),
            assertValid: static fn() => $options->assertValid(),
        );

        return new HttpBuilder($this, $options);
    }

    public function addSession(): SessionBuilder
    {
        $options = new SessionOptions();
        $this->registerCapability(Capability::SESSION, new SessionStackProvider($options));

        return new SessionBuilder($options);
    }

    public function addView(): ViewBuilder
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
        $this->capabilityProviders[$name] = $provider;

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
     * App / third-party brick (options configured on the brick before this call).
     */
    public function addCustom(BrickInterface $brick): self
    {
        $this->capabilities->register($brick->name(), $brick->requires());
        $this->customBricks[$brick->name()] = $brick;
        $this->optionValidators[] = static fn() => $brick->assertValid();

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

    /**
     * @return list<ServiceProviderInterface>
     */
    public function providers(): array
    {
        foreach ($this->optionValidators as $assertValid) {
            $assertValid();
        }

        $providers = [];
        foreach ($this->capabilities->orderedNames() as $name) {
            if (isset($this->customBricks[$name])) {
                array_push($providers, ...$this->customBricks[$name]->providers());
                continue;
            }

            $providers[] = $this->capabilityProviders[$name];
        }

        return [...$providers, ...$this->additionalProviders];
    }
}
