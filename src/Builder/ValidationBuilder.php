<?php declare(strict_types=1);

namespace Concept\Stack\Builder;

use Concept\Extensions\ValidationRakit\Contracts\RuleInterface;
use Concept\Stack\Builder\Contracts\StackCapabilityBuilder;
use Concept\Stack\Capability\Capability;
use Concept\Stack\Options\ValidationOptions;
use Concept\Stack\Providers\ValidationStackProvider;

final class ValidationBuilder implements StackCapabilityBuilder
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

    public function logFile(string $logFilePath): self
    {
        $this->options->setLogFilePath($logFilePath);
        $this->options->setLogEnabled(true);

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

    public function end(): StackBuilder
    {
        $this->parent->registerCapability(
            Capability::VALIDATION,
            new ValidationStackProvider($this->options),
        );

        return $this->parent;
    }
}
