<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\ErrorHandling;

use Closure;
use Concept\Extensions\ErrorHandlerWhoops\Contracts\ExceptionReporterInterface;
use Concept\Extensions\ErrorHandlerWhoops\Contracts\HttpErrorRendererInterface;
use Concept\Stack\Builder\StackBuilder;
use Concept\Stack\Capability\Capability;
use League\Container\DefinitionContainerInterface;
use Whoops\Handler\HandlerInterface;
use Whoops\Handler\PrettyPageHandler;

final class ErrorHandlingBuilder
{
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
    public function debugExceptionPage(): self
    {
        $this->options->setDebugExceptionPage(true);
        $this->options->setDebugHttpHandler(static fn(): PrettyPageHandler => new PrettyPageHandler());

        return $this;
    }

    /**
     * Report exceptions via php error_log + LoggerInterface. Requires withLogging().
     */
    public function reportToLog(): self
    {
        $this->options->setReportToLog(true);
        $this->parent->require(Capability::ERROR_HANDLING, Capability::LOGGING);

        return $this;
    }

    /**
     * HTML error pages via view (@ns/errors/{code}) + PHP fallback; JSON when request expects it.
     * Requires withView(). XOR with renderJson().
     */
    public function renderErrorPage(string $fallbackPath): self
    {
        $this->options->setErrorPageFallbackPath($fallbackPath);
        $this->parent->require(Capability::ERROR_HANDLING, Capability::VIEW);

        return $this;
    }

    /**
     * JSON-only HTTP errors (API without view). Requires withHttp(). XOR with renderErrorPage().
     */
    public function renderJson(): self
    {
        $this->options->setRenderJson(true);
        $this->parent->require(Capability::ERROR_HANDLING, Capability::HTTP);

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
}
