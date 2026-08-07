<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\ErrorHandling;

use Closure;
use Concept\Core\Container\ContainerDependency;
use Concept\Extensions\ErrorHandlerWhoops\Contracts\ExceptionReporterInterface;
use Concept\Extensions\ErrorHandlerWhoops\Contracts\HttpErrorRendererInterface;
use Concept\Stack\Bricks\ErrorHandling\Reporting\LoggerExceptionReporter;
use Concept\Stack\Bricks\ErrorHandling\Reporting\PhpErrorLogReporter;
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

final class ErrorHandlingBuilder
{
    private const string ERR_RENDERER_ALREADY_SET = 'Capability "error-handling" renderer is already set.';
    private const string ERR_REPORT_CHANNELS_EMPTY = 'Capability "error-handling" setLogReporting() requires at least one channel: logger or phpErrorLog.';

    public function __construct(
        private readonly StackBuilder $parent,
        private readonly ErrorHandlingOptions $options,
    ) {}

    /**
     * Debug display. Optional Whoops handler for non-JSON HTTP (e.g. new PrettyPageHandler()).
     * Handler is unused when $debug is false. JSON requests still use the safe renderer.
     *
     * @param HandlerInterface|(Closure(): HandlerInterface)|null $handler
     */
    public function setDebug(bool $debug = true, HandlerInterface|Closure|null $handler = null): self
    {
        $this->options->setDebug($debug);

        if ($handler !== null) {
            $this->options->setDebugHttpHandler($handler);
        }

        return $this;
    }

    /**
     * Report recipe: choose log channels (defaults: both).
     * logger → LoggerInterface (requires addLogging()); phpErrorLog → PHP error_log.
     */
    public function setLogReporting(bool $logger = true, bool $phpErrorLog = true): self
    {
        if (!$logger && !$phpErrorLog) {
            throw new InvalidCapabilityOptionsException(self::ERR_REPORT_CHANNELS_EMPTY);
        }

        if ($logger) {
            $this->parent->require(Capability::ERROR_HANDLING, Capability::LOGGING);
        }

        $this->options->setReporter(static function(DefinitionContainerInterface $container) use ($logger, $phpErrorLog): ExceptionReporterInterface {
            if (!$logger) {
                return new PhpErrorLogReporter();
            }

            return new LoggerExceptionReporter(
                logger: ContainerDependency::get($container, LoggerInterface::class),
                phpErrorLogReporter: $phpErrorLog ? new PhpErrorLogReporter() : null,
            );
        });

        return $this;
    }

    /**
     * Report escape hatch: custom ExceptionReporterInterface or factory.
     *
     * @param ExceptionReporterInterface|Closure(DefinitionContainerInterface): ExceptionReporterInterface $reporter
     */
    public function setReporter(ExceptionReporterInterface|Closure $reporter): self
    {
        $this->options->setReporter($reporter);

        return $this;
    }

    /**
     * Render recipe: ViewHttpErrorRenderer (@ns/errors/{code} + PHP fallback; JSON by Accept).
     * Requires addView(). Optional $fallbackPath — absolute dir with {code}.php; empty skips PHP files.
     * XOR with withJsonRenderer().
     */
    public function withViewRenderer(string $fallbackPath = ''): self
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
     * Render recipe: JsonHttpErrorRenderer (API without view). Requires addHttp(). XOR with withViewRenderer().
     */
    public function withJsonRenderer(): self
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
     * Render escape hatch: custom HttpErrorRendererInterface or factory.
     *
     * @param HttpErrorRendererInterface|Closure(DefinitionContainerInterface): HttpErrorRendererInterface $renderer
     */
    public function setRenderer(HttpErrorRendererInterface|Closure $renderer): self
    {
        $this->options->setRenderer($renderer);

        return $this;
    }

    private function assertRendererUnset(): void
    {
        if ($this->options->hasRenderer()) {
            throw new InvalidCapabilityOptionsException(self::ERR_RENDERER_ALREADY_SET);
        }
    }
}
