<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Http;

use Closure;
use Concept\Core\Http\Contracts\RouteInterceptorInterface;
use Concept\Stack\Builder\StackBuilder;
use Concept\Stack\Capability\Capability;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

final class HttpBuilder
{
    public function __construct(
        private readonly StackBuilder $parent,
        private readonly HttpOptions $options,
    ) {}

    /**
     * @param list<string> $routes Absolute paths to route files
     */
    public function setRoutes(array $routes): self
    {
        $this->options->setRoutes($routes);

        return $this;
    }

    /**
     * @param list<RouteInterceptorInterface|Closure(ContainerInterface): mixed> $interceptors
     */
    public function setInterceptors(array $interceptors): self
    {
        $this->options->setInterceptors($interceptors);

        return $this;
    }

    /**
     * Opt-in form request resolving. Makes HTTP depend on the validation
     * capability, which must be enabled via ConceptStack::addValidation().
     */
    public function withFormRequests(): self
    {
        $this->options->setFormRequests(true);
        $this->parent->require(Capability::HTTP, Capability::VALIDATION);

        return $this;
    }

    /**
     * Opt-in typed route parameters (casting). Makes HTTP depend on the casting
     * capability, which must be enabled via ConceptStack::addCasting().
     */
    public function withTypedRouteParameters(): self
    {
        $this->options->setTypedRouteParameters(true);
        $this->parent->require(Capability::HTTP, Capability::CASTING);

        return $this;
    }

    /**
     * Maps to HttpKernelServiceProvider::$notFoundMiddleware. Unset (null) means the
     * core default behaviour (League NotFoundException).
     *
     * Pass a middleware instance or a factory Closure; class-strings are not resolved.
     *
     * @param MiddlewareInterface|Closure(ContainerInterface): mixed|null $middleware
     */
    public function setNotFoundMiddleware(MiddlewareInterface|Closure|null $middleware): self
    {
        $this->options->setNotFoundMiddleware($middleware);

        return $this;
    }
}
