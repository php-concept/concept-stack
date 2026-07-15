<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Telemetry;

use Concept\Stack\Builder\StackBuilder;
use Concept\Stack\Capability\Capability;
use League\Event\ListenerSubscriber;

final class TelemetryBuilder
{
    public function __construct(
        private readonly StackBuilder $parent,
        private readonly TelemetryOptions $options,
    ) {}

    public function enabled(bool $enabled = true): self
    {
        $this->options->setEnabled($enabled);

        return $this;
    }

    /**
     * Opt-in Monolog → TelemetryCollector bridge via LogHandlerRegistry.
     * Requires the logging capability via ConceptStack::withLogging().
     */
    public function logs(bool $logs = true): self
    {
        $this->options->setLogs($logs);

        if ($logs) {
            $this->parent->require(Capability::TELEMETRY, Capability::LOGGING);
        }

        return $this;
    }

    /**
     * Declares intent to emit DB query events. Requires withDatabase().
     * Enable emission via DatabaseBuilder::withQueryTelemetry().
     */
    public function dbQueries(bool $dbQueries = true): self
    {
        $this->options->setDbQueries($dbQueries);

        if ($dbQueries) {
            $this->parent->require(Capability::TELEMETRY, Capability::DATABASE);
        }

        return $this;
    }

    /**
     * Event name used by TelemetryLogHandler when logs() is enabled.
     * Pass an application string — do not import Concept\App constants into stack.
     */
    public function eventName(string $eventName): self
    {
        $this->options->setEventName($eventName);

        return $this;
    }

    /**
     * @param list<class-string<ListenerSubscriber>> $subscribers
     */
    public function subscribers(array $subscribers): self
    {
        $this->options->setSubscribers($subscribers);

        return $this;
    }
}
