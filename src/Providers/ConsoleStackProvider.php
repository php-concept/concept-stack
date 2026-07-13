<?php declare(strict_types=1);

namespace Concept\Stack\Providers;

use Concept\Extensions\ConsoleSymfony\ConsoleSymfonyServiceProvider;
use Concept\Stack\Options\ConsoleOptions;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;

final class ConsoleStackProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    public function __construct(
        private readonly ConsoleOptions $options,
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
        $this->getContainer()->addServiceProvider(new ConsoleSymfonyServiceProvider(
            appName: $this->options->appName(),
            appVersion: $this->options->appVersion(),
            commands: $this->options->commands(),
        ));
    }
}
