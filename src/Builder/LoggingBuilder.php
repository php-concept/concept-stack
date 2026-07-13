<?php declare(strict_types=1);

namespace Concept\Stack\Builder;

use Concept\Stack\Builder\Contracts\StackCapabilityBuilder;
use Concept\Stack\Capability\Capability;
use Concept\Stack\Exceptions\InvalidCapabilityOptionsException;
use Concept\Stack\Options\LoggingOptions;
use Concept\Stack\Providers\LoggingStackProvider;

final class LoggingBuilder implements StackCapabilityBuilder
{
    public function __construct(
        private readonly StackBuilder $parent,
        private readonly LoggingOptions $options,
    ) {}

    public function file(string $logFilePath): self
    {
        $this->options->setLogFilePath($logFilePath);

        return $this;
    }

    public function level(string $level): self
    {
        $this->options->setLevel($level);

        return $this;
    }

    public function maxFiles(int $maxFiles): self
    {
        $this->options->setMaxFiles($maxFiles);

        return $this;
    }

    public function channel(string $channel): self
    {
        $this->options->setChannel($channel);

        return $this;
    }

    /**
     * Opt-in log masking. Makes logging depend on the masking capability,
     * which must be enabled via ConceptStack::withMasking().
     */
    public function withMasking(): self
    {
        $this->options->setMasking(true);

        return $this;
    }

    public function end(): StackBuilder
    {
        if ($this->options->logFilePath() === '') {
            throw InvalidCapabilityOptionsException::missingOption(Capability::LOGGING, 'file');
        }

        $requires = $this->options->masking() ? [Capability::MASKING] : [];

        $this->parent->registerCapability(
            Capability::LOGGING,
            new LoggingStackProvider($this->options),
            $requires,
        );

        return $this->parent;
    }
}
