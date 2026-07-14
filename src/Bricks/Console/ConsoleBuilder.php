<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Console;

use Symfony\Component\Console\Command\Command;

final class ConsoleBuilder
{
    public function __construct(
        private readonly ConsoleOptions $options,
    ) {}

    public function name(string $appName): self
    {
        $this->options->setAppName($appName);

        return $this;
    }

    public function version(string $appVersion): self
    {
        $this->options->setAppVersion($appVersion);

        return $this;
    }

    /**
     * @param list<class-string<Command>> $commands
     */
    public function commands(array $commands): self
    {
        $this->options->setCommands($commands);

        return $this;
    }
}
