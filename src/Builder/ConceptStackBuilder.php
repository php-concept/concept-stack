<?php declare(strict_types=1);

namespace Concept\Stack\Builder;

use Closure;
use Concept\Stack\Layer\StackLayer;
use Concept\Stack\Options\StackLayerOptions;
use Concept\Stack\Provider\StackProviderRegistry;
use League\Container\ServiceProvider\ServiceProviderInterface;
use RuntimeException;

final class ConceptStackBuilder
{
    private const array LAYER_ORDER = [
        StackLayer::FOUNDATION,
        StackLayer::LOGGING,
        StackLayer::TELEMETRY,
        StackLayer::VALIDATION,
        StackLayer::DATABASE,
        StackLayer::SESSION,
        StackLayer::HTTP,
        StackLayer::CONSOLE,
        StackLayer::VIEW,
        StackLayer::TWIG_ERRORS,
        StackLayer::JSON_ERRORS,
        StackLayer::COMPONENTS,
        StackLayer::RUNTIME,
    ];

    /** @var array<string, true> */
    private array $enabledLayers = [];

    /** @var array<string, array<string, mixed>> */
    private array $layerOptions = [];

    /** @var array<string, true> */
    private array $removedLayers = [];

    /** @var list<ServiceProviderInterface|Closure(string): mixed> */
    private array $prependedProviders = [];

    /** @var list<ServiceProviderInterface|Closure(string): mixed> */
    private array $appendedProviders = [];

    private StackProviderRegistry $providers;

    public function __construct(
        private readonly string $root,
        StackProviderRegistry $providers,
    ) {
        $this->providers = clone $providers;
    }

    public function withFoundation(): self
    {
        $this->enable(StackLayer::FOUNDATION);

        return $this;
    }

    public function withLogging(): self
    {
        $this->withFoundation();
        $this->enable(StackLayer::LOGGING);

        return $this;
    }

    public function withTelemetry(): self
    {
        $this->withFoundation();
        $this->enable(StackLayer::TELEMETRY);

        return $this;
    }

    public function withValidation(): self
    {
        $this->withFoundation();
        $this->enable(StackLayer::VALIDATION);

        return $this;
    }

    public function withFlashValidation(): self
    {
        return $this
            ->withValidation()
            ->withSession()
            ->withTwig();
    }

    /**
     * @param list<string>|null $routePaths
     */
    public function withHttp(?array $routePaths = null, bool $minimal = false): self
    {
        if (!$minimal) {
            $this->withFoundation();
        }

        $options = ['minimal' => $minimal];
        if ($routePaths !== null) {
            $options['routePaths'] = $routePaths;
        }

        $this->enable(StackLayer::HTTP, $options);

        return $this;
    }

    public function withSession(): self
    {
        $this->withFoundation();
        $this->enable(StackLayer::SESSION);

        return $this;
    }

    public function withTwig(): self
    {
        $this->withHttp();
        $this->enable(StackLayer::VIEW);

        return $this;
    }

    public function withPretty404(): self
    {
        $this->withTwig();
        $this->enable(StackLayer::TWIG_ERRORS);
        $this->disable(StackLayer::JSON_ERRORS);

        return $this;
    }

    public function withJsonErrors(): self
    {
        $this->withHttp();
        $this->enable(StackLayer::JSON_ERRORS);
        $this->disable(StackLayer::TWIG_ERRORS);

        return $this;
    }

    public function withDatabase(): self
    {
        $this->withFoundation();
        $this->enable(StackLayer::DATABASE);

        return $this;
    }

    /**
     * @param list<class-string> $commands
     */
    public function withConsole(?string $appName = null, ?string $appVersion = null, array $commands = []): self
    {
        $this->withFoundation();
        $this->enable(StackLayer::CONSOLE, [
            'appName' => $appName,
            'appVersion' => $appVersion,
            'commands' => $commands,
        ]);

        return $this;
    }

    public function withComponents(): self
    {
        $this->withFoundation();
        $this->enable(StackLayer::COMPONENTS);

        return $this;
    }

    public function withRuntime(): self
    {
        $this->withFoundation();
        $this->enable(StackLayer::RUNTIME);

        return $this;
    }

    public function without(string $layer): self
    {
        $this->disable($layer);

        return $this;
    }

    /**
     * @param Closure(string, StackLayerOptions): ServiceProviderInterface $factory
     */
    public function replace(string $layer, Closure $factory): self
    {
        $this->providers->replace($layer, $factory);
        unset($this->removedLayers[$layer]);

        return $this;
    }

    /**
     * @param ServiceProviderInterface|Closure(string): mixed $provider
     */
    public function prepend(ServiceProviderInterface|Closure $provider): self
    {
        $this->prependedProviders[] = $provider;

        return $this;
    }

    /**
     * @param ServiceProviderInterface|Closure(string): mixed $provider
     */
    public function append(ServiceProviderInterface|Closure $provider): self
    {
        $this->appendedProviders[] = $provider;

        return $this;
    }

    /**
     * @return list<ServiceProviderInterface>
     */
    public function providers(): array
    {
        $providers = [];

        foreach (self::LAYER_ORDER as $layer) {
            $this->push($providers, $layer);
        }

        return $this->withCustomProviders($providers);
    }

    /**
     * @param array<string, mixed> $options
     */
    private function enable(string $layer, array $options = []): void
    {
        $this->enabledLayers[$layer] = true;
        unset($this->removedLayers[$layer]);

        if ($options !== []) {
            $this->layerOptions[$layer] = [
                ...($this->layerOptions[$layer] ?? []),
                ...$options,
            ];
        }
    }

    private function disable(string $layer): void
    {
        unset($this->enabledLayers[$layer], $this->layerOptions[$layer]);
        $this->removedLayers[$layer] = true;
    }

    /**
     * @param list<ServiceProviderInterface> $providers
     */
    private function push(array &$providers, string $layer): void
    {
        if (!isset($this->enabledLayers[$layer]) || isset($this->removedLayers[$layer])) {
            return;
        }

        $providers[] = $this->provider($layer, $this->layerOptions[$layer] ?? []);
    }

    /**
     * @param array<string, mixed> $options
     */
    private function provider(string $layer, array $options = []): ServiceProviderInterface
    {
        return $this->providers->make($layer, $this->root, $options);
    }

    /**
     * @param list<ServiceProviderInterface> $providers
     * @return list<ServiceProviderInterface>
     */
    private function withCustomProviders(array $providers): array
    {
        return [
            ...array_map(fn(ServiceProviderInterface|Closure $provider): ServiceProviderInterface => $this->customProvider($provider), $this->prependedProviders),
            ...$providers,
            ...array_map(fn(ServiceProviderInterface|Closure $provider): ServiceProviderInterface => $this->customProvider($provider), $this->appendedProviders),
        ];
    }

    /**
     * @param ServiceProviderInterface|Closure(string): mixed $provider
     */
    private function customProvider(ServiceProviderInterface|Closure $provider): ServiceProviderInterface
    {
        if ($provider instanceof ServiceProviderInterface) {
            return $provider;
        }

        $resolved = $provider($this->root);
        if (!$resolved instanceof ServiceProviderInterface) {
            throw new RuntimeException('Custom stack provider factory must return a service provider.');
        }

        return $resolved;
    }
}
