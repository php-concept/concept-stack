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
    private const string ERR_RENDERER_XOR = 'Capability "error-handling" accepts only one renderer: renderErrorPage() or renderJson().';

    private bool $debug = false;

    private bool $debugExceptionPage = false;

    private bool $reportToLog = false;

    private bool $renderJson = false;

    private ?string $errorPageFallbackPath = null;

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

    public function debugExceptionPage(): bool
    {
        return $this->debugExceptionPage;
    }

    public function setDebugExceptionPage(bool $debugExceptionPage): void
    {
        $this->debugExceptionPage = $debugExceptionPage;
    }

    public function reportToLog(): bool
    {
        return $this->reportToLog;
    }

    public function setReportToLog(bool $reportToLog): void
    {
        $this->reportToLog = $reportToLog;
    }

    public function renderJson(): bool
    {
        return $this->renderJson;
    }

    public function setRenderJson(bool $renderJson): void
    {
        if ($renderJson && $this->errorPageFallbackPath !== null) {
            throw new InvalidCapabilityOptionsException(self::ERR_RENDERER_XOR);
        }

        $this->renderJson = $renderJson;
    }

    public function errorPageFallbackPath(): ?string
    {
        return $this->errorPageFallbackPath;
    }

    public function setErrorPageFallbackPath(string $fallbackPath): void
    {
        if ($this->renderJson) {
            throw new InvalidCapabilityOptionsException(self::ERR_RENDERER_XOR);
        }

        $this->errorPageFallbackPath = $fallbackPath;
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
        if ($this->reporter === null && !$this->reportToLog) {
            throw InvalidCapabilityOptionsException::missingOption(
                Capability::ERROR_HANDLING,
                'reportToLog()/reporter()',
            );
        }

        if ($this->renderer === null && $this->errorPageFallbackPath === null && !$this->renderJson) {
            throw InvalidCapabilityOptionsException::missingOption(
                Capability::ERROR_HANDLING,
                'renderErrorPage()/renderJson()/renderer()',
            );
        }
    }
}
