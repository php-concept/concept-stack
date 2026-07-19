<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\ErrorHandling;

use Closure;
use Concept\Core\Container\ContainerDependency;
use Concept\Extensions\ErrorHandlerWhoops\Contracts\ExceptionReporterInterface;
use Concept\Extensions\ErrorHandlerWhoops\Contracts\HttpErrorRendererInterface;
use Concept\Stack\Bricks\ErrorHandling\Reporting\LoggerExceptionReporter;
use Concept\Stack\Bricks\ErrorHandling\Rendering\JsonHttpErrorRenderer;
use Concept\Stack\Bricks\ErrorHandling\Rendering\ViewHttpErrorRenderer;
use Concept\Stack\Builder\StackBuilder;
use Concept\Stack\Capability\Capability;
use Concept\Stack\Exceptions\InvalidCapabilityOptionsException;
use Concept\Extensions\Http\Contracts\ResponseFactoryInterface;
use Concept\Extensions\Http\Requests\RequestFormat;
use Concept\Extensions\LoggerMonolog\Contracts\LoggerInterface;
use Concept\Extensions\View\Contracts\ViewResponseFactoryInterface;
use Concept\Extensions\View\Support\ViewRouteNamespaceResolver;
use League\Container\DefinitionContainerInterface;
use Whoops\Handler\HandlerInterface;
use Whoops\Handler\PrettyPageHandler;

final class ErrorHandlingBuilder
{
    private const string ERR_RENDERER_ALREADY_SET = 'Capability "error-handling" renderer is already set.';

    public function __construct(
        private readonly StackBuilder $parent,
        private readonly ErrorHandlingOptions $options,
    ) {}

    public function debug(bool $debug = true): self
    {
        $this->options->setDebug($debug);

        return $this;
    }

    /**
     * Whoops exception page for debug web requests (non-JSON). Used only when debug(true).
     */
    public function showDebugExceptionPage(): self
    {
        $this->options->setDebugHttpHandler(static fn(): PrettyPageHandler => new PrettyPageHandler());

        return $this;
    }

    /**
     * Stack recipe: LoggerExceptionReporter. Requires withLogging().
     */
    public function reportToLog(): self
    {
        $this->parent->require(Capability::ERROR_HANDLING, Capability::LOGGING);
        $this->options->setReporter(static function(DefinitionContainerInterface $container): ExceptionReporterInterface {
            return new LoggerExceptionReporter(
                logger: ContainerDependency::get($container, LoggerInterface::class),
            );
        });

        return $this;
    }

    /**
     * Stack recipe: ViewHttpErrorRenderer (@ns/errors/{code} + PHP fallback; JSON by Accept).
     * Requires withView(). Optional $fallbackPath — absolute dir with {code}.php; empty skips PHP files.
     */
    public function renderHtmlErrorPage(string $fallbackPath = ''): self
    {
        $this->assertRendererUnset();
        $this->parent->require(Capability::ERROR_HANDLING, Capability::VIEW);
        $this->options->setRenderer(static function(DefinitionContainerInterface $container) use ($fallbackPath): HttpErrorRendererInterface {
            return new ViewHttpErrorRenderer(
                responseFactory: ContainerDependency::get($container, ResponseFactoryInterface::class),
                viewResponse: ContainerDependency::get($container, ViewResponseFactoryInterface::class),
                requestFormat: ContainerDependency::get($container, RequestFormat::class),
                routeNamespaceResolver: ContainerDependency::get($container, ViewRouteNamespaceResolver::class),
                exceptionReporter: ContainerDependency::get($container, ExceptionReporterInterface::class),
                fallbackPath: $fallbackPath,
            );
        });

        return $this;
    }

    /**
     * Stack recipe: JsonHttpErrorRenderer (API without view). Requires withHttp(). XOR with renderHtmlErrorPage().
     */
    public function renderJson(): self
    {
        $this->assertRendererUnset();
        $this->parent->require(Capability::ERROR_HANDLING, Capability::HTTP);
        $this->options->setRenderer(static function(DefinitionContainerInterface $container): HttpErrorRendererInterface {
            return new JsonHttpErrorRenderer(
                responseFactory: ContainerDependency::get($container, ResponseFactoryInterface::class),
            );
        });

        return $this;
    }

    /**
     * @param Closure(DefinitionContainerInterface): ExceptionReporterInterface $factory
     */
    public function exceptionReporter(Closure $factory): self
    {
        $this->options->setReporter($factory);

        return $this;
    }

    /**
     * @param Closure(DefinitionContainerInterface): HttpErrorRendererInterface $factory
     */
    public function httpErrorRenderer(Closure $factory): self
    {
        $this->options->setRenderer($factory);

        return $this;
    }

    /**
     * @param ExceptionReporterInterface|Closure(DefinitionContainerInterface): ExceptionReporterInterface $reporter
     */
    public function reporter(ExceptionReporterInterface|Closure $reporter): self
    {
        $this->options->setReporter($reporter);

        return $this;
    }

    /**
     * @param HttpErrorRendererInterface|Closure(DefinitionContainerInterface): HttpErrorRendererInterface $renderer
     */
    public function renderer(HttpErrorRendererInterface|Closure $renderer): self
    {
        $this->options->setRenderer($renderer);

        return $this;
    }

    /**
     * @param HandlerInterface|Closure(): HandlerInterface $handler
     */
    public function debugHttpHandler(HandlerInterface|Closure $handler): self
    {
        $this->options->setDebugHttpHandler($handler);

        return $this;
    }

    private function assertRendererUnset(): void
    {
        if ($this->options->hasRenderer()) {
            throw new InvalidCapabilityOptionsException(self::ERR_RENDERER_ALREADY_SET);
        }
    }
}
