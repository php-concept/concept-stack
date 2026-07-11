<?php declare(strict_types=1);

namespace Concept\Stack\Provider\Layer;

use Concept\Extensions\ConsoleSymfony\ConsoleSymfonyServiceProvider;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use Symfony\Component\Console\Command\Command;

final class ConsoleLayerProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    /**
     * @param list<class-string<Command>> $commands
     */
    public function __construct(
        private readonly string $appName,
        private readonly string $appVersion,
        private readonly array $commands,
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
            appName: $this->appName,
            appVersion: $this->appVersion,
            commands: $this->commands,
        ));
    }
}
