<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\ErrorHandling;

use Closure;
use Concept\Extensions\ErrorHandlerWhoops\Contracts\ExceptionReporterInterface;
use Concept\Extensions\ErrorHandlerWhoops\Contracts\HttpErrorRendererInterface;
use Concept\Stack\Capability\Capability;
use Concept\Stack\Exceptions\InvalidCapabilityOptionsException;
use League\Container\DefinitionContainerInterface;
use Whoops\Handler\HandlerInterface;

final class ErrorHandlingOptions
{
    private bool $debug = false;

    /** @var ExceptionReporterInterface|Closure(DefinitionContainerInterface): ExceptionReporterInterface|null */
    private ExceptionReporterInterface|Closure|null $reporter = null;

    /** @var HttpErrorRendererInterface|Closure(DefinitionContainerInterface): HttpErrorRendererInterface|null */
    private HttpErrorRendererInterface|Closure|null $renderer = null;

    /** @var HandlerInterface|Closure(): HandlerInterface|null */
    private HandlerInterface|Closure|null $debugHttpHandler = null;

    public function debug(): bool
    {
        return $this->debug;
    }

    public function setDebug(bool $debug): void
    {
        $this->debug = $debug;
    }

    public function hasReporter(): bool
    {
        return $this->reporter !== null;
    }

    public function hasRenderer(): bool
    {
        return $this->renderer !== null;
    }

    /**
     * @return ExceptionReporterInterface|Closure(DefinitionContainerInterface): ExceptionReporterInterface|null
     */
    public function reporter(): ExceptionReporterInterface|Closure|null
    {
        return $this->reporter;
    }

    /**
     * @param ExceptionReporterInterface|Closure(DefinitionContainerInterface): ExceptionReporterInterface $reporter
     */
    public function setReporter(ExceptionReporterInterface|Closure $reporter): void
    {
        $this->reporter = $reporter;
    }

    /**
     * @return HttpErrorRendererInterface|Closure(DefinitionContainerInterface): HttpErrorRendererInterface|null
     */
    public function renderer(): HttpErrorRendererInterface|Closure|null
    {
        return $this->renderer;
    }

    /**
     * @param HttpErrorRendererInterface|Closure(DefinitionContainerInterface): HttpErrorRendererInterface $renderer
     */
    public function setRenderer(HttpErrorRendererInterface|Closure $renderer): void
    {
        $this->renderer = $renderer;
    }

    /**
     * @return HandlerInterface|Closure(): HandlerInterface|null
     */
    public function debugHttpHandler(): HandlerInterface|Closure|null
    {
        return $this->debugHttpHandler;
    }

    /**
     * @param HandlerInterface|Closure(): HandlerInterface $handler
     */
    public function setDebugHttpHandler(HandlerInterface|Closure $handler): void
    {
        $this->debugHttpHandler = $handler;
    }

    public function assertValid(): void
    {
        if ($this->reporter === null) {
            throw InvalidCapabilityOptionsException::missingOption(
                Capability::ERROR_HANDLING,
                'reportToLog()/reporter()',
            );
        }

        if ($this->renderer === null) {
            throw InvalidCapabilityOptionsException::missingOption(
                Capability::ERROR_HANDLING,
                'renderHtmlErrorPage()/renderJson()/renderer()',
            );
        }
    }
}
