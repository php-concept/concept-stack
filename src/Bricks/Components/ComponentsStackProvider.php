<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Components;

use Concept\Core\Container\ContainerDependency;
use Concept\Extensions\Components\Commands\ComponentListCommand;
use Concept\Extensions\Components\Commands\ComponentPublishAssetsCommand;
use Concept\Extensions\Components\ComponentRegistry;
use Concept\Extensions\Components\ComponentsServiceProvider;
use Concept\Extensions\DatabaseEloquent\Registries\MigrationRegistry;
use Concept\Extensions\DatabaseEloquent\Registries\SeederRegistry;
use Concept\Extensions\View\Registry\ViewRegistry;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use League\Route\Router;
use Symfony\Component\Console\Application as ConsoleApplication;

final class ComponentsStackProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    public function __construct(
        private readonly ComponentsOptions $options,
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

        $container->addServiceProvider(new ComponentsServiceProvider(
            componentClasses: $this->options->componentClasses(),
            seedersRegistrar: $this->options->database()
                ? fn(ComponentRegistry $registry) => ContainerDependency::get(
                    $container,
                    SeederRegistry::class,
                )->append($registry->seeders())
                : null,
            migrationsRegistrar: $this->options->database()
                ? fn(ComponentRegistry $registry) => ContainerDependency::get(
                    $container,
                    MigrationRegistry::class,
                )->append($registry->migrationPaths())
                : null,
            commandsRegistrar: $this->options->console()
                ? function(ComponentRegistry $registry) use ($container): void {
                    $console = ContainerDependency::get($container, ConsoleApplication::class);
                    $console->addCommand(new ComponentListCommand($registry));
                    $console->addCommand(new ComponentPublishAssetsCommand($registry));

                    foreach ($registry->commands() as $commandClass) {
                        $console->addCommand(ContainerDependency::get($container, $commandClass));
                    }
                }
                : null,
            routesRegistrar: $this->options->http()
                ? function(ComponentRegistry $registry) use ($container): void {
                    $router = ContainerDependency::get($container, Router::class);

                    foreach ($registry->routes() as $routesFile) {
                        require $routesFile;
                    }
                }
                : null,
            viewFeaturesRegistrar: $this->options->view()
                ? function(ComponentRegistry $registry) use ($container): void {
                    $viewRegistry = ContainerDependency::get($container, ViewRegistry::class);
                    $viewRegistry->extensions()->append($registry->viewExtensions());
                    $viewRegistry->paths()->append($registry->viewPaths());
                    $viewRegistry->routeNamespace()->append($registry->viewRouteNamespace());
                }
                : null,
        ));
    }
}
