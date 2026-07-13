<?php declare(strict_types=1);

namespace Concept\Stack\Options;

use Closure;
use Concept\Core\Http\Contracts\RouteInterceptorInterface;
use Psr\Http\Server\MiddlewareInterface;

final class HttpOptions
{
    /** @var list<string> */
    private array $routes = [];

    /** @var list<class-string<RouteInterceptorInterface>> */
    private array $interceptors = [];

    private string $cacheDirectory = '';

    /** @var list<class-string> */
    private array $transformerClasses = [];

    private bool $debug = false;

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

    public function cacheDirectory(): string
    {
        return $this->cacheDirectory;
    }

    public function setCacheDirectory(string $cacheDirectory): void
    {
        $this->cacheDirectory = $cacheDirectory;
    }

    /**
     * @return list<class-string>
     */
    public function transformerClasses(): array
    {
        return $this->transformerClasses;
    }

    /**
     * @param list<class-string> $transformerClasses
     */
    public function setTransformerClasses(array $transformerClasses): void
    {
        $this->transformerClasses = $transformerClasses;
    }

    public function debug(): bool
    {
        return $this->debug;
    }

    public function setDebug(bool $debug): void
    {
        $this->debug = $debug;
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
}
