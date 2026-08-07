<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Telemetry;

use Concept\Stack\Builder\StackBuilder;
use Concept\Stack\Capability\Capability;

final class TelemetryBuilder
{
    public function __construct(
        private readonly StackBuilder $parent,
        private readonly TelemetryOptions $options,
    ) {}

    public function setEnabled(bool $enabled = true): self
    {
        $this->options->setEnabled($enabled);

        return $this;
    }

    /**
     * Opt-in Monolog → TelemetryCollector bridge via LogHandlerRegistry.
     * Requires the logging capability via ConceptStack::addLogging().
     */
    public function setLogs(bool $logs = true): self
    {
        $this->options->setLogs($logs);

        if ($logs) {
            $this->parent->require(Capability::TELEMETRY, Capability::LOGGING);
        }

        return $this;
    }

    /**
     * Event name used by TelemetryLogHandler when setLogs() is enabled.
     * Pass an application string — do not import Concept\App constants into stack.
     */
    public function setEventName(string $eventName): self
    {
        $this->options->setEventName($eventName);

        return $this;
    }
}
