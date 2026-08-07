<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Database;

use Concept\Stack\Capability\Capability;
use Concept\Stack\Exceptions\InvalidCapabilityOptionsException;

final class DatabaseOptions
{
    /** @var array<string, mixed> */
    private array $connection = [];

    /** @var list<string> */
    private array $migrationPaths = [];

    private string $migrationsTable = 'migrations';

    /** @var list<class-string> */
    private array $seeders = [];

    private bool $queryLogEnabled = false;

    private string $queryLogFilePath = '';

    private int $queryLogMaxFiles = 7;

    private bool $queryLogMasking = false;

    private bool $emitQueryEvents = false;

    /**
     * @return array<string, mixed>
     */
    public function connection(): array
    {
        return $this->connection;
    }

    /**
     * @param array<string, mixed> $connection
     */
    public function setConnection(array $connection): void
    {
        $this->connection = $connection;
    }

    /**
     * @return list<string>
     */
    public function migrationPaths(): array
    {
        return $this->migrationPaths;
    }

    /**
     * @param list<string> $migrationPaths Absolute paths to migration directories
     */
    public function setMigrationPaths(array $migrationPaths): void
    {
        $this->migrationPaths = $migrationPaths;
    }

    public function migrationsTable(): string
    {
        return $this->migrationsTable;
    }

    public function setMigrationsTable(string $migrationsTable): void
    {
        $this->migrationsTable = $migrationsTable;
    }

    /**
     * @return list<class-string>
     */
    public function seeders(): array
    {
        return $this->seeders;
    }

    /**
     * @param list<class-string> $seeders
     */
    public function setSeeders(array $seeders): void
    {
        $this->seeders = $seeders;
    }

    public function queryLogEnabled(): bool
    {
        return $this->queryLogEnabled;
    }

    public function setQueryLogEnabled(bool $queryLogEnabled): void
    {
        $this->queryLogEnabled = $queryLogEnabled;
    }

    public function queryLogFilePath(): string
    {
        return $this->queryLogFilePath;
    }

    public function setQueryLogFilePath(string $queryLogFilePath): void
    {
        $this->queryLogFilePath = $queryLogFilePath;
    }

    public function queryLogMaxFiles(): int
    {
        return $this->queryLogMaxFiles;
    }

    public function setQueryLogMaxFiles(int $queryLogMaxFiles): void
    {
        $this->queryLogMaxFiles = $queryLogMaxFiles;
    }

    public function queryLogMasking(): bool
    {
        return $this->queryLogMasking;
    }

    public function setQueryLogMasking(bool $queryLogMasking): void
    {
        $this->queryLogMasking = $queryLogMasking;
    }

    public function emitQueryEvents(): bool
    {
        return $this->emitQueryEvents;
    }

    public function setEmitQueryEvents(bool $emitQueryEvents): void
    {
        $this->emitQueryEvents = $emitQueryEvents;
    }

    public function assertValid(): void
    {
        if ($this->connection === []) {
            throw InvalidCapabilityOptionsException::missingOption(Capability::DATABASE, 'setConnection()');
        }
    }
}
