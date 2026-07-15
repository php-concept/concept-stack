<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\ErrorHandling\Reporting;

use Concept\Core\Container\ContainerDependency;
use Concept\Extensions\ErrorHandlerWhoops\Contracts\ExceptionReporterInterface;
use Concept\Extensions\LoggerMonolog\Contracts\LoggerInterface;
use League\Container\DefinitionContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

/**
 * Stack recipe: php error_log + LoggerInterface. Requires the logging capability.
 */
final class LoggerExceptionReporter implements ExceptionReporterInterface
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly DefinitionContainerInterface $container,
        private readonly ExceptionReporterInterface $phpErrorLogReporter = new PhpErrorLogReporter(),
    ) {}

    public function report(Throwable $exception, string $uri = '', bool $bootstrap = false): void
    {
        if ($uri === '') {
            $uri = $this->resolveRequestUri();
        }

        $this->phpErrorLogReporter->report($exception, $uri, $bootstrap);
        $this->logger->exception($exception, $uri);
    }

    private function resolveRequestUri(): string
    {
        if (!$this->container->has(ServerRequestInterface::class)) {
            return '';
        }

        $request = ContainerDependency::get($this->container, ServerRequestInterface::class);

        return $request->getUri()->getPath();
    }
}
