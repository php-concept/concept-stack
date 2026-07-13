<?php declare(strict_types=1);

namespace Concept\Stack\Options;

use Symfony\Component\Console\Command\Command;

final class ConsoleOptions
{
    private string $appName = '';

    private string $appVersion = '';

    /** @var list<class-string<Command>> */
    private array $commands = [];

    public function appName(): string
    {
        return $this->appName;
    }

    public function setAppName(string $appName): void
    {
        $this->appName = $appName;
    }

    public function appVersion(): string
    {
        return $this->appVersion;
    }

    public function setAppVersion(string $appVersion): void
    {
        $this->appVersion = $appVersion;
    }

    /**
     * @return list<class-string<Command>>
     */
    public function commands(): array
    {
        return $this->commands;
    }

    /**
     * @param list<class-string<Command>> $commands
     */
    public function setCommands(array $commands): void
    {
        $this->commands = $commands;
    }
}
