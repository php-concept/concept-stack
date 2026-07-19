<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Telemetry;

use Concept\Core\Container\ContainerDependency;
use Concept\Extensions\LoggerMonolog\LogHandlerRegistry;
use Concept\Extensions\Telemetry\Handlers\TelemetryLogHandler;
use Concept\Extensions\Telemetry\TelemetryCollector;
use Concept\Extensions\Telemetry\TelemetryServiceProvider as TelemetryExtensionServiceProvider;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;

final class TelemetryStackProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    public function __construct(
        private readonly TelemetryOptions $options,
    ) {}

    public function provides(string $id): bool
    {
        return $id === TelemetryLogHandler::class;
    }

    public function register(): void
    {
        $this->getContainer()->addServiceProvider(new TelemetryExtensionServiceProvider());
    }

    public function boot(): void
    {
        if (!$this->options->enabled()) {
            return;
        }

        $container = $this->getContainer();

        if ($this->options->logs()) {
            $eventName = $this->options->eventName();

            $container->add(TelemetryLogHandler::class, function() use ($container, $eventName): TelemetryLogHandler {
                return new TelemetryLogHandler(
                    collector: ContainerDependency::get($container, TelemetryCollector::class),
                    eventName: $eventName,
                );
            })->setShared(true);

            if ($container->has(LogHandlerRegistry::class)) {
                $registry = ContainerDependency::get($container, LogHandlerRegistry::class);
                $registry->add(TelemetryLogHandler::class);
            }
        }

    }
}
