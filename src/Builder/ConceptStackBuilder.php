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
    private bool $minimalHttp = false;
    private bool $foundation = false;
    private bool $logging = false;
    private bool $telemetry = false;
    private bool $validation = false;
    private bool $database = false;
    private bool $session = false;
    private bool $http = false;
    private bool $console = false;
    private bool $view = false;
    private bool $twigErrors = false;
    private bool $jsonErrors = false;
    private bool $components = false;
    private bool $runtime = false;

    /** @var array<string, true> */
    private array $removedLayers = [];

    /** @var list<ServiceProviderInterface|Closure(string): mixed> */
    private array $prependedProviders = [];

    /** @var list<ServiceProviderInterface|Closure(string): mixed> */
    private array $appendedProviders = [];

    /** @var list<string>|null */
    private ?array $routePaths = null;

    private StackProviderRegistry $providers;

    public function __construct(
        private readonly string $root,
        StackProviderRegistry $providers,
    ) {
        $this->providers = clone $providers;
    }

    public function minimalHttp(): self
    {
        $this->minimalHttp = true;

        return $this;
    }

    public function withFoundation(): self
    {
        $this->minimalHttp = false;
        $this->foundation = true;

        return $this;
    }

    public function withLogging(): self
    {
        $this->withFoundation();
        $this->logging = true;

        return $this;
    }

    public function withTelemetry(): self
    {
        $this->withFoundation();
        $this->telemetry = true;

        return $this;
    }

    public function withValidation(): self
    {
        $this->withFoundation();
        $this->validation = true;

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
    public function withHttp(?array $routePaths = null): self
    {
        $this->withFoundation();
        $this->http = true;

        if ($routePaths !== null) {
            $this->routePaths = $routePaths;
        }

        return $this;
    }

    public function withSession(): self
    {
        $this->withFoundation();
        $this->session = true;

        return $this;
    }

    public function withTwig(): self
    {
        $this->withHttp();
        $this->view = true;

        return $this;
    }

    public function withPretty404(): self
    {
        $this->withTwig();
        $this->twigErrors = true;
        $this->jsonErrors = false;

        return $this;
    }

    public function withJsonErrors(): self
    {
        $this->withHttp();
        $this->jsonErrors = true;
        $this->twigErrors = false;

        return $this;
    }

    public function withDatabase(): self
    {
        $this->withFoundation();
        $this->database = true;

        return $this;
    }

    public function withConsole(): self
    {
        $this->withFoundation();
        $this->console = true;

        return $this;
    }

    public function withComponents(): self
    {
        $this->withFoundation();
        $this->components = true;

        return $this;
    }

    public function withRuntime(): self
    {
        $this->withFoundation();
        $this->runtime = true;

        return $this;
    }

    public function without(string $layer): self
    {
        $this->removedLayers[$layer] = true;

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
        if ($this->minimalHttp) {
            return $this->withCustomProviders([
                $this->provider(StackLayer::MINIMAL_HTTP),
            ]);
        }

        $providers = [];

        $this->push($providers, StackLayer::FOUNDATION, $this->foundation);
        $this->push($providers, StackLayer::LOGGING, $this->logging);
        $this->push($providers, StackLayer::TELEMETRY, $this->telemetry);
        $this->push($providers, StackLayer::VALIDATION, $this->validation);
        $this->push($providers, StackLayer::DATABASE, $this->database);
        $this->push($providers, StackLayer::SESSION, $this->session);
        $this->push($providers, StackLayer::HTTP, $this->http, ['routePaths' => $this->routePaths]);
        $this->push($providers, StackLayer::CONSOLE, $this->console);
        $this->push($providers, StackLayer::VIEW, $this->view);
        $this->push($providers, StackLayer::TWIG_ERRORS, $this->twigErrors);
        $this->push($providers, StackLayer::JSON_ERRORS, $this->jsonErrors);
        $this->push($providers, StackLayer::COMPONENTS, $this->components);
        $this->push($providers, StackLayer::RUNTIME, $this->runtime);

        return $this->withCustomProviders($providers);
    }

    /**
     * @param list<ServiceProviderInterface> $providers
     * @param array<string, mixed> $options
     */
    private function push(array &$providers, string $layer, bool $enabled, array $options = []): void
    {
        if (!$enabled || isset($this->removedLayers[$layer])) {
            return;
        }

        $providers[] = $this->provider($layer, $options);
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
