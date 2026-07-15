<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\ErrorHandling;

use Closure;
use Concept\Core\Container\ContainerDependency;
use Concept\Extensions\ErrorHandlerWhoops\Contracts\ExceptionReporterInterface;
use Concept\Extensions\ErrorHandlerWhoops\Contracts\HttpErrorRendererInterface;
use Concept\Extensions\ErrorHandlerWhoops\ErrorHandlerWhoopsServiceProvider;
use Concept\Extensions\ErrorHandlerWhoops\Handlers\RenderHttpErrorHandler;
use Concept\Extensions\ErrorHandlerWhoops\Handlers\ReportExceptionHandler;
use Concept\Extensions\Http\Contracts\ResponseFactoryInterface;
use Concept\Extensions\Http\Requests\RequestFormat;
use Concept\Extensions\LoggerMonolog\Contracts\LoggerInterface;
use Concept\Extensions\View\Contracts\ViewResponseFactoryInterface;
use Concept\Extensions\View\Support\ViewRouteNamespaceResolver;
use Concept\Stack\Bricks\ErrorHandling\Reporting\LoggerExceptionReporter;
use Concept\Stack\Bricks\ErrorHandling\Rendering\JsonHttpErrorRenderer;
use Concept\Stack\Bricks\ErrorHandling\Rendering\ViewHttpErrorRenderer;
use League\Container\DefinitionContainerInterface;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use Psr\Http\Message\ServerRequestInterface;
use Whoops\Handler\HandlerInterface;
use Whoops\Handler\PlainTextHandler;

final class ErrorHandlingStackProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    public function __construct(
        private readonly ErrorHandlingOptions $options,
    ) {}

    public function provides(string $id): bool
    {
        return false;
    }

    public function register(): void
    {
    }

    public function boot(): void
    {
        $container = $this->getContainer();

        $container->add(
            ExceptionReporterInterface::class,
            fn(): ExceptionReporterInterface => $this->resolveReporter($container),
        )->setShared(true);

        $container->add(
            HttpErrorRendererInterface::class,
            fn(): HttpErrorRendererInterface => $this->resolveRenderer($container),
        )->setShared(true);

        $container->addServiceProvider(new ErrorHandlerWhoopsServiceProvider(
            handlers: $this->buildAwakeHandlers($container),
        ));
    }

    private function resolveReporter(DefinitionContainerInterface $container): ExceptionReporterInterface
    {
        $custom = $this->options->reporter();

        if ($custom instanceof ExceptionReporterInterface) {
            return $custom;
        }

        if ($custom instanceof Closure) {
            return $custom($container);
        }

        return new LoggerExceptionReporter(
            logger: ContainerDependency::get($container, LoggerInterface::class),
            container: $container,
        );
    }

    private function resolveRenderer(DefinitionContainerInterface $container): HttpErrorRendererInterface
    {
        $custom = $this->options->renderer();

        if ($custom instanceof HttpErrorRendererInterface) {
            return $custom;
        }

        if ($custom instanceof Closure) {
            return $custom($container);
        }

        if ($this->options->renderJson()) {
            return new JsonHttpErrorRenderer(
                responseFactory: ContainerDependency::get($container, ResponseFactoryInterface::class),
            );
        }

        $fallbackPath = $this->options->errorPageFallbackPath();
        assert($fallbackPath !== null);

        return new ViewHttpErrorRenderer(
            responseFactory: ContainerDependency::get($container, ResponseFactoryInterface::class),
            viewResponse: ContainerDependency::get($container, ViewResponseFactoryInterface::class),
            requestFormat: ContainerDependency::get($container, RequestFormat::class),
            routeNamespaceResolver: ContainerDependency::get($container, ViewRouteNamespaceResolver::class),
            exceptionReporter: ContainerDependency::get($container, ExceptionReporterInterface::class),
            fallbackPath: $fallbackPath,
        );
    }

    /**
     * @return list<HandlerInterface>
     */
    private function buildAwakeHandlers(DefinitionContainerInterface $container): array
    {
        $handlers = [
            new ReportExceptionHandler(
                fn(): ExceptionReporterInterface => ContainerDependency::get($container, ExceptionReporterInterface::class),
            ),
        ];

        if (PHP_SAPI === 'cli') {
            $handlers[] = new PlainTextHandler();

            return $handlers;
        }

        $debugHandler = $this->resolveDebugHttpHandler();

        if ($this->options->debug() && $debugHandler !== null && !$this->requestExpectsJson($container)) {
            $handlers[] = $debugHandler;

            return $handlers;
        }

        $handlers[] = new RenderHttpErrorHandler(
            fn(): HttpErrorRendererInterface => ContainerDependency::get($container, HttpErrorRendererInterface::class),
            $container,
        );

        return $handlers;
    }

    private function resolveDebugHttpHandler(): ?HandlerInterface
    {
        $handler = $this->options->debugHttpHandler();

        if ($handler === null) {
            return null;
        }

        if ($handler instanceof HandlerInterface) {
            return $handler;
        }

        return $handler();
    }

    private function requestExpectsJson(DefinitionContainerInterface $container): bool
    {
        if (!$container->has(ServerRequestInterface::class) || !$container->has(RequestFormat::class)) {
            return false;
        }

        $request = ContainerDependency::get($container, ServerRequestInterface::class);
        $requestFormat = ContainerDependency::get($container, RequestFormat::class);

        return $requestFormat->expectsJson($request);
    }
}
