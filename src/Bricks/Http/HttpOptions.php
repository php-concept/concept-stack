<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Http;

use Closure;
use Concept\Core\Http\Contracts\RouteInterceptorInterface;
use Concept\Stack\Capability\Capability;
use Concept\Stack\Exceptions\InvalidCapabilityOptionsException;
use Psr\Http\Server\MiddlewareInterface;

final class HttpOptions
{
    /** @var list<string> */
    private array $routes = [];

    /** @var list<class-string<RouteInterceptorInterface>> */
    private array $interceptors = [];

    private bool $typedRouteParameters = false;

    private bool $formRequests = false;

    /** @var MiddlewareInterface|class-string<MiddlewareInterface>|Closure|null */
    private MiddlewareInterface|Closure|string|null $notFoundMiddleware = null;

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
     * @return list<class-string<RouteInterceptorInterface>>
     */
    public function interceptors(): array
    {
        return $this->interceptors;
    }

    /**
     * @param list<class-string<RouteInterceptorInterface>> $interceptors
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
     * @return MiddlewareInterface|class-string<MiddlewareInterface>|Closure|null
     */
    public function notFoundMiddleware(): MiddlewareInterface|Closure|string|null
    {
        return $this->notFoundMiddleware;
    }

    /**
     * @param MiddlewareInterface|class-string<MiddlewareInterface>|Closure|null $notFoundMiddleware
     */
    public function setNotFoundMiddleware(MiddlewareInterface|Closure|string|null $notFoundMiddleware): void
    {
        $this->notFoundMiddleware = $notFoundMiddleware;
    }

    public function assertValid(): void
    {
        if ($this->routes === []) {
            throw InvalidCapabilityOptionsException::missingOption(Capability::HTTP, 'routes');
        }
    }
}
