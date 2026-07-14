<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Session;

use Concept\Core\Container\ContainerDependency;
use Concept\Extensions\Csrf\CsrfServiceProvider;
use Concept\Extensions\SessionSymfony\Contracts\SessionInterface;
use Concept\Extensions\SessionSymfony\SessionServiceProvider;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;

final class SessionStackProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    public function __construct(
        private readonly SessionOptions $options,
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
        $container = $this->getContainer();

        $container->addServiceProvider(new SessionServiceProvider(
            sessionOptions: $this->options->sessionOptions(),
            handler: $this->options->handler() ?? new NativeFileSessionHandler(),
        ));

        if (!$this->options->csrf()) {
            return;
        }

        $container->addServiceProvider(new CsrfServiceProvider(
            sessionFactory: fn(): SessionInterface => ContainerDependency::get($container, SessionInterface::class),
        ));
    }
}
