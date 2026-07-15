<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Telemetry;

use Concept\Stack\Capability\Capability;
use Concept\Stack\Exceptions\InvalidCapabilityOptionsException;
use League\Event\ListenerSubscriber;

final class TelemetryOptions
{
    private bool $enabled = true;

    private bool $logs = false;

    private bool $dbQueries = false;

    private string $eventName = '';

    /** @var list<class-string<ListenerSubscriber>> */
    private array $subscribers = [];

    public function enabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function logs(): bool
    {
        return $this->logs;
    }

    public function setLogs(bool $logs): void
    {
        $this->logs = $logs;
    }

    public function dbQueries(): bool
    {
        return $this->dbQueries;
    }

    public function setDbQueries(bool $dbQueries): void
    {
        $this->dbQueries = $dbQueries;
    }

    public function eventName(): string
    {
        return $this->eventName;
    }

    public function setEventName(string $eventName): void
    {
        $this->eventName = $eventName;
    }

    /**
     * @return list<class-string<ListenerSubscriber>>
     */
    public function subscribers(): array
    {
        return $this->subscribers;
    }

    /**
     * @param list<class-string<ListenerSubscriber>> $subscribers
     */
    public function setSubscribers(array $subscribers): void
    {
        $this->subscribers = $subscribers;
    }

    public function assertValid(): void
    {
        if (!$this->enabled) {
            return;
        }

        if ($this->logs && $this->eventName === '') {
            throw InvalidCapabilityOptionsException::missingOption(Capability::TELEMETRY, 'eventName');
        }
    }
}
