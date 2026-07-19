<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Events;

use League\Event\ListenerSubscriber;

final class EventsOptions
{
    /** @var list<class-string<ListenerSubscriber>> */
    private array $subscribers = [];

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
}
