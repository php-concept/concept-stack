<?php declare(strict_types=1);

namespace Concept\Stack\Builder;

use Concept\Stack\Builder\Contracts\StackCapabilityBuilder;
use Concept\Stack\Capability\Capability;
use Concept\Stack\Options\ConsoleOptions;
use Concept\Stack\Providers\ConsoleStackProvider;
use Symfony\Component\Console\Command\Command;

final class ConsoleBuilder implements StackCapabilityBuilder
{
    public function __construct(
        private readonly StackBuilder $parent,
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

    public function end(): StackBuilder
    {
        $this->parent->registerCapability(Capability::CONSOLE, new ConsoleStackProvider($this->options));

        return $this->parent;
    }
}
