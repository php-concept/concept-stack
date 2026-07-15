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

    /** @var Closure(DefinitionContainerInterface): ExceptionReporterInterface|null */
    private ?Closure $exceptionReporterFactory = null;

    /** @var Closure(DefinitionContainerInterface): HttpErrorRendererInterface|null */
    private ?Closure $httpErrorRendererFactory = null;

    /** @var Closure(): HandlerInterface|null */
    private ?Closure $debugHttpHandlerFactory = null;

    public function debug(): bool
    {
        return $this->debug;
    }

    public function setDebug(bool $debug): void
    {
        $this->debug = $debug;
    }

    /**
     * @return Closure(DefinitionContainerInterface): ExceptionReporterInterface|null
     */
    public function exceptionReporterFactory(): ?Closure
    {
        return $this->exceptionReporterFactory;
    }

    /**
     * @param Closure(DefinitionContainerInterface): ExceptionReporterInterface $factory
     */
    public function setExceptionReporterFactory(Closure $factory): void
    {
        $this->exceptionReporterFactory = $factory;
    }

    /**
     * @return Closure(DefinitionContainerInterface): HttpErrorRendererInterface|null
     */
    public function httpErrorRendererFactory(): ?Closure
    {
        return $this->httpErrorRendererFactory;
    }

    /**
     * @param Closure(DefinitionContainerInterface): HttpErrorRendererInterface $factory
     */
    public function setHttpErrorRendererFactory(Closure $factory): void
    {
        $this->httpErrorRendererFactory = $factory;
    }

    /**
     * @return Closure(): HandlerInterface|null
     */
    public function debugHttpHandlerFactory(): ?Closure
    {
        return $this->debugHttpHandlerFactory;
    }

    /**
     * @param Closure(): HandlerInterface $factory
     */
    public function setDebugHttpHandlerFactory(Closure $factory): void
    {
        $this->debugHttpHandlerFactory = $factory;
    }

    public function assertValid(): void
    {
        if ($this->exceptionReporterFactory === null) {
            throw InvalidCapabilityOptionsException::missingOption(Capability::ERROR_HANDLING, 'exceptionReporter');
        }

        if ($this->httpErrorRendererFactory === null) {
            throw InvalidCapabilityOptionsException::missingOption(Capability::ERROR_HANDLING, 'httpErrorRenderer');
        }
    }
}
