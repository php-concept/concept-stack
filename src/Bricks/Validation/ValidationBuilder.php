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
    public function setRules(array $customRules): self
    {
        $this->options->setCustomRules($customRules);

        return $this;
    }

    /**
     * Opt-in validation result logging to a rotating file.
     */
    public function withLogging(string $logFilePath, int $maxFiles = 7): self
    {
        $this->options->setLogEnabled(true);
        $this->options->setLogFilePath($logFilePath);
        $this->options->setLogMaxFiles($maxFiles);

        return $this;
    }

    /**
     * @param list<string> $globalExcept
     */
    public function setGlobalExcept(array $globalExcept): self
    {
        $this->options->setGlobalExcept($globalExcept);

        return $this;
    }

    /**
     * Opt-in validation log masking. Requires ConceptStack::addMasking().
     */
    public function withMasking(): self
    {
        $this->parent->require(Capability::VALIDATION, Capability::MASKING);

        return $this;
    }
}
