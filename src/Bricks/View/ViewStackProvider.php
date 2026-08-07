<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\View;

use Concept\Core\Container\ContainerDependency;
use Concept\Core\Http\Contracts\RequestContextInterface;
use Concept\Extensions\Http\Contracts\ResponseFactoryInterface;
use Concept\Extensions\View\Contracts\ViewInterface;
use Concept\Extensions\View\ViewServiceProvider;
use Concept\Extensions\ViewPlates\PlatesViewServiceProvider;
use Concept\Extensions\ViewTwig\TwigViewServiceProvider;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use LogicException;

final class ViewStackProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    private const string ERR_UNKNOWN_ENGINE = 'Unknown view engine "%s".';

    public function __construct(
        private readonly ViewOptions $options,
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

        $container->addServiceProvider(new ViewServiceProvider(
            responseFactoryFactory: fn(): ResponseFactoryInterface => ContainerDependency::get(
                $container,
                ResponseFactoryInterface::class,
            ),
            viewFactory: fn(): ViewInterface => ContainerDependency::get($container, ViewInterface::class),
            requestContextFactory: fn(): RequestContextInterface => ContainerDependency::get(
                $container,
                RequestContextInterface::class,
            ),
            paths: $this->options->paths(),
            extensions: $this->options->extensions(),
            routeNamespace: $this->options->routeNamespaces(),
        ));

        match ($this->options->engine()) {
            ViewOptions::ENGINE_TWIG => $container->addServiceProvider(new TwigViewServiceProvider(
                viewsPath: $this->options->twigViewsPath(),
                cacheDir: $this->options->twigCacheDir(),
                debug: $this->options->twigDebug(),
                defaultExtension: $this->options->twigDefaultExtension()
                    ?? TwigViewServiceProvider::DEFAULT_EXTENSION,
            )),
            ViewOptions::ENGINE_PLATES => $container->addServiceProvider(new PlatesViewServiceProvider(
                viewsPath: $this->options->platesViewsPath(),
                defaultExtension: $this->options->platesDefaultExtension()
                    ?? PlatesViewServiceProvider::DEFAULT_EXTENSION,
            )),
            default => throw new LogicException(sprintf(
                self::ERR_UNKNOWN_ENGINE,
                (string) $this->options->engine(),
            )),
        };
    }
}
