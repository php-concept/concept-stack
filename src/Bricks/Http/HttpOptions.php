<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Http;

use Closure;
use Concept\Core\Http\Contracts\ArgumentResolverInterface;
use Concept\Core\Http\Contracts\RouteInterceptorInterface;
use Concept\Stack\Capability\Capability;
use Concept\Stack\Exceptions\InvalidCapabilityOptionsException;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

final class HttpOptions
{
    /** @var list<string> */
    private array $routes = [];

    /** @var list<RouteInterceptorInterface|Closure(ContainerInterface): mixed> */
    private array $interceptors = [];

    private bool $typedRouteParameters = false;

    private bool $formRequests = false;

    /** @var MiddlewareInterface|Closure(ContainerInterface): mixed|null */
    private MiddlewareInterface|Closure|null $notFoundMiddleware = null;

    /** @var list<ArgumentResolverInterface|Closure(ContainerInterface): mixed> */
    private array $appendedResolvers = [];

    /**
     * @return list<string>
     */
    public function routes(): array
    {
        return $this->routes;
    }

    /**
     * @param list<string> $routes
     */
    public function setRoutes(array $routes): void
    {
        $this->routes = $routes;
    }

    /**
     * @return list<RouteInterceptorInterface|Closure(ContainerInterface): mixed>
     */
    public function interceptors(): array
    {
        return $this->interceptors;
    }

    /**
     * @param list<RouteInterceptorInterface|Closure(ContainerInterface): mixed> $interceptors
     */
    public function setInterceptors(array $interceptors): void
    {
        $this->interceptors = $interceptors;
    }

    public function typedRouteParameters(): bool
    {
        return $this->typedRouteParameters;
    }

    public function setTypedRouteParameters(bool $typedRouteParameters): void
    {
        $this->typedRouteParameters = $typedRouteParameters;
    }

    public function formRequests(): bool
    {
        return $this->formRequests;
    }

    public function setFormRequests(bool $formRequests): void
    {
        $this->formRequests = $formRequests;
    }

    /**
     * @return MiddlewareInterface|Closure(ContainerInterface): mixed|null
     */
    public function notFoundMiddleware(): MiddlewareInterface|Closure|null
    {
        return $this->notFoundMiddleware;
    }

    /**
     * @param MiddlewareInterface|Closure(ContainerInterface): mixed|null $notFoundMiddleware
     */
    public function setNotFoundMiddleware(MiddlewareInterface|Closure|null $notFoundMiddleware): void
    {
        $this->notFoundMiddleware = $notFoundMiddleware;
    }

    /**
     * @return list<ArgumentResolverInterface|Closure(ContainerInterface): mixed>
     */
    public function appendedResolvers(): array
    {
        return $this->appendedResolvers;
    }

    /**
     * @param list<ArgumentResolverInterface|Closure(ContainerInterface): mixed> $resolvers
     */
    public function appendResolvers(array $resolvers): void
    {
        array_push($this->appendedResolvers, ...$resolvers);
    }

    public function assertValid(): void
    {
        if ($this->routes === []) {
            throw InvalidCapabilityOptionsException::missingOption(Capability::HTTP, 'setRoutes()');
        }
    }
}
