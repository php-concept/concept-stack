<?php declare(strict_types=1);

namespace Concept\Stack\Options;

use Concept\Extensions\ValidationRakit\Contracts\RuleInterface;

final class ValidationOptions
{
    /** @var array<string, class-string<RuleInterface>> */
    private array $customRules = [];

    private bool $logEnabled = false;

    private string $logFilePath = '';

    private int $logMaxFiles = 7;

    /** @var list<string> */
    private array $globalExcept = [];

    /**
     * @return array<string, class-string<RuleInterface>>
     */
    public function customRules(): array
    {
        return $this->customRules;
    }

    /**
     * @param array<string, class-string<RuleInterface>> $customRules
     */
    public function setCustomRules(array $customRules): void
    {
        $this->customRules = $customRules;
    }

    public function logEnabled(): bool
    {
        return $this->logEnabled;
    }

    public function setLogEnabled(bool $logEnabled): void
    {
        $this->logEnabled = $logEnabled;
    }

    public function logFilePath(): string
    {
        return $this->logFilePath;
    }

    public function setLogFilePath(string $logFilePath): void
    {
        $this->logFilePath = $logFilePath;
    }

    public function logMaxFiles(): int
    {
        return $this->logMaxFiles;
    }

    public function setLogMaxFiles(int $logMaxFiles): void
    {
        $this->logMaxFiles = $logMaxFiles;
    }

    /**
     * @return list<string>
     */
    public function globalExcept(): array
    {
        return $this->globalExcept;
    }

    /**
     * @param list<string> $globalExcept
     */
    public function setGlobalExcept(array $globalExcept): void
    {
        $this->globalExcept = $globalExcept;
    }
}
