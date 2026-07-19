<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Events;

use Concept\Extensions\Event\EventServiceProvider;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;

final class EventsStackProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    public function __construct(
        private readonly EventsOptions $options,
    ) {}

    public function provides(string $id): bool
    {
        return false;
    }

    public function register(): void
    {
    }

    public function boot(): void
    {
        $this->getContainer()->addServiceProvider(new EventServiceProvider(
            subscriberClasses: $this->options->subscribers(),
        ));
    }
}
