<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Logging;

use Concept\Stack\Builder\Contracts\StackCapabilityBuilder;
use Concept\Stack\Builder\StackBuilder;
use Concept\Stack\Capability\Capability;
use Concept\Stack\Exceptions\InvalidCapabilityOptionsException;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Throwable;

final class LoggingBuilder implements StackCapabilityBuilder
{
    public function __construct(
        private readonly StackBuilder $parent,
        private readonly LoggingOptions $options,
    ) {}

    /**
     * Default level for subsequent toRotatingFile() / toStderr() helpers.
     */
    public function level(string $level): self
    {
        $this->options->setLevel($level);

        return $this;
    }

    public function channel(string $channel): self
    {
        $this->options->setChannel($channel);

        return $this;
    }

    public function toRotatingFile(string $path, int $maxFiles = 7, ?string $level = null): self
    {
        $this->options->addHandler(new RotatingFileHandler(
            $path,
            $maxFiles,
            $this->resolveLevel($level),
        ));

        return $this;
    }

    public function toStderr(?string $level = null): self
    {
        $this->options->addHandler(new StreamHandler(
            'php://stderr',
            $this->resolveLevel($level),
        ));

        return $this;
    }

    /**
     * Escape hatch for any ready Monolog handler.
     */
    public function toHandler(HandlerInterface $handler): self
    {
        $this->options->addHandler($handler);

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
        if ($this->options->handlers() === []) {
            throw InvalidCapabilityOptionsException::missingHandlers(Capability::LOGGING);
        }

        $requires = $this->options->masking() ? [Capability::MASKING] : [];

        $this->parent->registerCapability(
            Capability::LOGGING,
            new LoggingStackProvider($this->options),
            $requires,
        );

        return $this->parent;
    }

    private function resolveLevel(?string $level): Level
    {
        $name = $level ?? $this->options->level();

        try {
            /** @phpstan-ignore-next-line */
            return Level::fromName($name);
        } catch (Throwable) {
            return Level::Debug;
        }
    }
}
