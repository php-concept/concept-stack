<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Logging;

use Concept\Extensions\DataMasker\Contracts\DataMaskerInterface;
use Concept\Extensions\LoggerMonolog\LoggerMonologServiceProvider;
use Concept\Stack\Support\OptionalDependency;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;

final class LoggingStackProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    public function __construct(
        private readonly LoggingOptions $options,
    ) {}

    public function provides(string $id): bool
    {
        return false;
    }

    public function register(): void
    {
    }

    public function boot(): void
    {
        $container = $this->getContainer();

        $container->addServiceProvider(new LoggerMonologServiceProvider(
            handlers: $this->options->handlers(),
            channel: $this->options->channel(),
            dataMaskerFactory: $this->options->masking()
                ? OptionalDependency::factory($container, DataMaskerInterface::class)
                : null,
        ));
    }
}
