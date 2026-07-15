<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Database;

use Concept\Extensions\DataMasker\Contracts\DataMaskerInterface;
use Concept\Extensions\DatabaseEloquent\DatabaseEloquentServiceProvider;
use Concept\Extensions\DatabaseEloquent\PaginationConfiguratorServiceProvider;
use Concept\Stack\Support\OptionalDependency;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;

final class DatabaseStackProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    public function __construct(
        private readonly DatabaseOptions $options,
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

        $container->addServiceProvider(new PaginationConfiguratorServiceProvider());

        $container->addServiceProvider(new DatabaseEloquentServiceProvider(
            connection: $this->options->connection(),
            migrationPaths: $this->options->migrationPaths(),
            migrationsTable: $this->options->migrationsTable(),
            seeders: $this->options->seeders(),
            logEnabled: $this->options->queryLogEnabled(),
            logFilePath: $this->options->queryLogFilePath(),
            logMaxFiles: $this->options->queryLogMaxFiles(),
            dataMaskerFactory: $this->options->queryLogMasking()
                ? OptionalDependency::factory($container, DataMaskerInterface::class)
                : null,
            emitQueryEvents: $this->options->emitQueryEvents(),
        ));
    }
}
