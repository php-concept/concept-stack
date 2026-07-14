<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Logging;

use Monolog\Handler\HandlerInterface;

final class LoggingOptions
{
    /** @var list<HandlerInterface> */
    private array $handlers = [];

    private string $level = 'debug';

    private string $channel = 'app';

    private bool $masking = false;

    /**
     * @return list<HandlerInterface>
     */
    public function handlers(): array
    {
        return $this->handlers;
    }

    public function addHandler(HandlerInterface $handler): void
    {
        $this->handlers[] = $handler;
    }

    public function level(): string
    {
        return $this->level;
    }

    public function setLevel(string $level): void
    {
        $this->level = $level;
    }

    public function channel(): string
    {
        return $this->channel;
    }

    public function setChannel(string $channel): void
    {
        $this->channel = $channel;
    }

    public function masking(): bool
    {
        return $this->masking;
    }

    public function setMasking(bool $masking): void
    {
        $this->masking = $masking;
    }
}
