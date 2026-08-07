<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\ErrorHandling;

use Closure;
use Concept\Core\Container\ContainerDependency;
use Concept\Extensions\ErrorHandlerWhoops\Contracts\ExceptionReporterInterface;
use Concept\Extensions\ErrorHandlerWhoops\Contracts\HttpErrorRendererInterface;
use Concept\Extensions\ErrorHandlerWhoops\ErrorHandlerWhoopsServiceProvider;
use Concept\Extensions\Http\Requests\RequestFormat;
use Concept\Stack\Capability\Capability;
use Concept\Stack\Exceptions\InvalidCapabilityOptionsException;
use League\Container\DefinitionContainerInterface;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use Psr\Http\Message\ServerRequestInterface;
use Whoops\Handler\HandlerInterface;

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

        $container->addServiceProvider(new ErrorHandlerWhoopsServiceProvider(
            exceptionReporterFactory: fn(): ExceptionReporterInterface => $this->resolveReporter($container),
            httpErrorRendererFactory: fn(): HttpErrorRendererInterface => $this->resolveRenderer($container),
            debugHttpHandlerFactory: $this->createDebugHttpHandlerFactory(),
        ));
    }

    private function resolveReporter(DefinitionContainerInterface $container): ExceptionReporterInterface
    {
        $reporter = $this->options->reporter();

        if ($reporter instanceof ExceptionReporterInterface) {
            return $reporter;
        }

        if ($reporter instanceof Closure) {
            return $reporter($container);
        }

        throw InvalidCapabilityOptionsException::missingOption(
            Capability::ERROR_HANDLING,
            'setLogReporting()/setReporter()',
        );
    }

    private function resolveRenderer(DefinitionContainerInterface $container): HttpErrorRendererInterface
    {
        $renderer = $this->options->renderer();

        if ($renderer instanceof HttpErrorRendererInterface) {
            return $renderer;
        }

        if ($renderer instanceof Closure) {
            return $renderer($container);
        }

        throw InvalidCapabilityOptionsException::missingOption(
            Capability::ERROR_HANDLING,
            'withViewRenderer()/withJsonRenderer()/setRenderer()',
        );
    }

    /**
     * @return null|(Closure(): ?HandlerInterface)
     */
    private function createDebugHttpHandlerFactory(): ?Closure
    {
        if (!$this->options->debug()) {
            return null;
        }

        $handler = $this->options->debugHttpHandler();
        if ($handler === null) {
            return null;
        }

        return function() use ($handler): ?HandlerInterface {
            if ($this->requestExpectsJson()) {
                return null;
            }

            if ($handler instanceof HandlerInterface) {
                return $handler;
            }

            return $handler();
        };
    }

    private function requestExpectsJson(): bool
    {
        $container = $this->getContainer();

        if (!$container->has(ServerRequestInterface::class) || !$container->has(RequestFormat::class)) {
            return false;
        }

        $request = ContainerDependency::get($container, ServerRequestInterface::class);
        $requestFormat = ContainerDependency::get($container, RequestFormat::class);

        return $requestFormat->expectsJson($request);
    }
}
