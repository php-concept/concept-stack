<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Validation;

use Concept\Extensions\ValidationRakit\Contracts\RuleInterface;
use Concept\Stack\Builder\StackBuilder;
use Concept\Stack\Capability\Capability;

final class ValidationBuilder
{
    public function __construct(
        private readonly StackBuilder $parent,
        private readonly ValidationOptions $options,
    ) {}

    /**
     * @param array<string, class-string<RuleInterface>> $customRules
     */
    public function rules(array $customRules): self
    {
        $this->options->setCustomRules($customRules);

        return $this;
    }

    /**
     * @param array<string, class-string<RuleInterface>> $customRules
     */
    public function customRules(array $customRules): self
    {
        return $this->rules($customRules);
    }

    public function logFile(string $logFilePath): self
    {
        $this->options->setLogFilePath($logFilePath);
        $this->options->setLogEnabled(true);

        return $this;
    }

    public function logFilePath(string $logFilePath): self
    {
        $this->options->setLogFilePath($logFilePath);

        return $this;
    }

    public function logEnabled(bool $logEnabled = true): self
    {
        $this->options->setLogEnabled($logEnabled);

        return $this;
    }

    public function logMaxFiles(int $logMaxFiles): self
    {
        $this->options->setLogMaxFiles($logMaxFiles);

        return $this;
    }

    /**
     * @param list<string> $globalExcept
     */
    public function globalExcept(array $globalExcept): self
    {
        $this->options->setGlobalExcept($globalExcept);

        return $this;
    }

    /**
     * Opt-in validation log masking. Requires ConceptStack::withMasking().
     */
    public function withMasking(): self
    {
        $this->parent->require(Capability::VALIDATION, Capability::MASKING);

        return $this;
    }
}
