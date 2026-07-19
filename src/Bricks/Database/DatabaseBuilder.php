<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Database;

use Concept\Stack\Builder\StackBuilder;
use Concept\Stack\Capability\Capability;

final class DatabaseBuilder
{
    public function __construct(
        private readonly StackBuilder $parent,
        private readonly DatabaseOptions $options,
    ) {}

    /**
     * Illuminate/Eloquent connection array (driver, host, database, username, password, …).
     *
     * @param array<string, mixed> $connection
     */
    public function connection(array $connection): self
    {
        $this->options->setConnection($connection);

        return $this;
    }

    /**
     * @param list<string> $paths Absolute paths to migration directories
     */
    public function migrations(array $paths): self
    {
        $this->options->setMigrationPaths($paths);

        return $this;
    }

    public function migrationsTable(string $table): self
    {
        $this->options->setMigrationsTable($table);

        return $this;
    }

    /**
     * @param list<class-string> $seeders
     */
    public function seeders(array $seeders): self
    {
        $this->options->setSeeders($seeders);

        return $this;
    }

    /**
     * Opt-in SQL query log (RotatingFileHandler inside DatabaseEloquentServiceProvider).
     */
    public function withQueryLogging(string $logFilePath, int $maxFiles = 7): self
    {
        $this->options->setQueryLogEnabled(true);
        $this->options->setQueryLogFilePath($logFilePath);
        $this->options->setQueryLogMaxFiles($maxFiles);

        return $this;
    }

    /**
     * Opt-in query log masking. Requires the masking capability via ConceptStack::withMasking().
     */
    public function withMasking(): self
    {
        $this->options->setQueryLogMasking(true);
        $this->parent->require(Capability::DATABASE, Capability::MASKING);

        return $this;
    }

    /**
     * Opt-in DatabaseQueryExecuted events. Requires the events capability.
     */
    public function withEmitQueryEvents(): self
    {
        $this->options->setEmitQueryEvents(true);
        $this->parent->require(Capability::DATABASE, Capability::EVENTS);

        return $this;
    }
}
