<?php declare(strict_types=1);

namespace Concept\Stack\Builder;

use Concept\Core\Http\Contracts\RouteInterceptorInterface;
use Concept\Stack\Builder\Contracts\StackCapabilityBuilder;
use Concept\Stack\Capability\Capability;
use Concept\Stack\Exceptions\InvalidCapabilityOptionsException;
use Closure;
use Concept\Stack\Options\HttpOptions;
use Concept\Stack\Providers\HttpStackProvider;
use Psr\Http\Server\MiddlewareInterface;

final class HttpBuilder implements StackCapabilityBuilder
{
    public function __construct(
        private readonly StackBuilder $parent,
        private readonly HttpOptions $options,
    ) {}

    /**
     * @param list<string> $routes Absolute paths to route files
     */
    public function routes(array $routes): self
    {
        $this->options->setRoutes($routes);

        return $this;
    }

    /**
     * @param list<class-string<RouteInterceptorInterface>> $interceptors
     */
    public function interceptors(array $interceptors): self
    {
        $this->options->setInterceptors($interceptors);

        return $this;
    }

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

    /**
     * Maps to HttpKernelServiceProvider::$notFoundMiddleware. Unset (null) means the
     * core default behaviour (League NotFoundException).
     *
     * @param MiddlewareInterface|class-string<MiddlewareInterface>|Closure|null $middleware
     */
    public function notFound(MiddlewareInterface|Closure|string|null $middleware): self
    {
        $this->options->setNotFoundMiddleware($middleware);

        return $this;
    }

    public function end(): StackBuilder
    {
        if ($this->options->routes() === []) {
            throw InvalidCapabilityOptionsException::missingOption(Capability::HTTP, 'routes');
        }

        if ($this->options->cacheDirectory() === '') {
            throw InvalidCapabilityOptionsException::missingOption(Capability::HTTP, 'cacheDir');
        }

        $this->parent->registerCapability(
            Capability::HTTP,
            new HttpStackProvider($this->options),
        );

        return $this->parent;
    }
}
