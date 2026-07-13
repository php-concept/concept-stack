<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Logging;

final class LoggingOptions
{
    private string $logFilePath = '';

    private string $level = 'debug';

    private int $maxFiles = 7;

    private string $channel = 'app';

    private bool $masking = false;

    public function logFilePath(): string
    {
        return $this->logFilePath;
    }

    public function setLogFilePath(string $logFilePath): void
    {
        $this->logFilePath = $logFilePath;
    }

    public function level(): string
    {
        return $this->level;
    }

    public function setLevel(string $level): void
    {
        $this->level = $level;
    }

    public function maxFiles(): int
    {
        return $this->maxFiles;
    }

    public function setMaxFiles(int $maxFiles): void
    {
        $this->maxFiles = $maxFiles;
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
