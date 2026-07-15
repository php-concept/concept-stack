<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\ErrorHandling;

use Concept\Core\Container\ContainerDependency;
use Concept\Extensions\ErrorHandlerWhoops\Contracts\ExceptionReporterInterface;
use Concept\Extensions\ErrorHandlerWhoops\Contracts\HttpErrorRendererInterface;
use Concept\Extensions\ErrorHandlerWhoops\ErrorHandlerWhoopsServiceProvider;
use Concept\Extensions\ErrorHandlerWhoops\Handlers\RenderHttpErrorHandler;
use Concept\Extensions\ErrorHandlerWhoops\Handlers\ReportExceptionHandler;
use Concept\Extensions\Http\Requests\RequestFormat;
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
        $reporterFactory = $this->options->exceptionReporterFactory();
        $rendererFactory = $this->options->httpErrorRendererFactory();

        if ($reporterFactory === null || $rendererFactory === null) {
            return;
        }

        $container->add(
            ExceptionReporterInterface::class,
            fn(): ExceptionReporterInterface => $reporterFactory($container),
        )->setShared(true);

        $container->add(
            HttpErrorRendererInterface::class,
            fn(): HttpErrorRendererInterface => $rendererFactory($container),
        )->setShared(true);

        $container->addServiceProvider(new ErrorHandlerWhoopsServiceProvider(
            handlers: $this->buildAwakeHandlers($container),
        ));
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

        $debugHandlerFactory = $this->options->debugHttpHandlerFactory();

        if ($this->options->debug() && $debugHandlerFactory !== null && !$this->requestExpectsJson($container)) {
            $handlers[] = $debugHandlerFactory();

            return $handlers;
        }

        $handlers[] = new RenderHttpErrorHandler(
            fn(): HttpErrorRendererInterface => ContainerDependency::get($container, HttpErrorRendererInterface::class),
            $container,
        );

        return $handlers;
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
