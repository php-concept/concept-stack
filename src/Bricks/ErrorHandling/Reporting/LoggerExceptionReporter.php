<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\ErrorHandling\Reporting;

use Concept\Extensions\ErrorHandlerWhoops\Contracts\ExceptionReporterInterface;
use Concept\Extensions\LoggerMonolog\Contracts\LoggerInterface;
use Throwable;

/**
 * Stack recipe: LoggerInterface, optionally also PHP error_log.
 * Requires the logging capability when used from setLogReporting(logger: true).
 */
final class LoggerExceptionReporter implements ExceptionReporterInterface
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ?ExceptionReporterInterface $phpErrorLogReporter = new PhpErrorLogReporter(),
    ) {}

    public function report(Throwable $exception, string $uri = '', bool $bootstrap = false): void
    {
        $this->phpErrorLogReporter?->report($exception, $uri, $bootstrap);
        $this->logger->exception($exception, $uri);
    }
}
