<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Telemetry;

use Concept\Stack\Capability\Capability;
use Concept\Stack\Exceptions\InvalidCapabilityOptionsException;

final class TelemetryOptions
{
    private bool $enabled = true;

    private bool $logs = false;

    private string $eventName = '';

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

    public function eventName(): string
    {
        return $this->eventName;
    }

    public function setEventName(string $eventName): void
    {
        $this->eventName = $eventName;
    }

    public function assertValid(): void
    {
        if (!$this->enabled) {
            return;
        }

        if ($this->logs && $this->eventName === '') {
            throw InvalidCapabilityOptionsException::missingOption(Capability::TELEMETRY, 'setEventName()');
        }
    }
}
