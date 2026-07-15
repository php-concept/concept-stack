<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\ErrorHandling;

use Closure;
use Concept\Extensions\ErrorHandlerWhoops\Contracts\ExceptionReporterInterface;
use Concept\Extensions\ErrorHandlerWhoops\Contracts\HttpErrorRendererInterface;
use League\Container\DefinitionContainerInterface;
use Whoops\Handler\HandlerInterface;

final class ErrorHandlingBuilder
{
    public function __construct(
        private readonly ErrorHandlingOptions $options,
    ) {}

    public function debug(bool $debug = true): self
    {
        $this->options->setDebug($debug);

        return $this;
    }

    /**
     * @param Closure(DefinitionContainerInterface): ExceptionReporterInterface $factory
     */
    public function exceptionReporter(Closure $factory): self
    {
        $this->options->setExceptionReporterFactory($factory);

        return $this;
    }

    /**
     * Application HTTP error pages (Twig / JSON / custom). Stack does not know Concept\App renderers.
     *
     * @param Closure(DefinitionContainerInterface): HttpErrorRendererInterface $factory
     */
    public function httpErrorRenderer(Closure $factory): self
    {
        $this->options->setHttpErrorRendererFactory($factory);

        return $this;
    }

    /**
     * Opt-in debug web handler (e.g. PrettyPageHandler). Production always uses httpErrorRenderer.
     *
     * @param Closure(): HandlerInterface $factory
     */
    public function debugHttpHandler(Closure $factory): self
    {
        $this->options->setDebugHttpHandlerFactory($factory);

        return $this;
    }
}
