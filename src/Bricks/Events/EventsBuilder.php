<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Events;

use League\Event\ListenerSubscriber;

final class EventsBuilder
{
    public function __construct(
        private readonly EventsOptions $options,
    ) {}

    /**
     * @param list<class-string<ListenerSubscriber>> $subscribers
     */
    public function subscribers(array $subscribers): self
    {
        $this->options->setSubscribers($subscribers);

        return $this;
    }
}
