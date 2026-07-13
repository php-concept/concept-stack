<?php declare(strict_types=1);

namespace Concept\Stack\Providers;

use Concept\Core\Container\ContainerDependency;
use Concept\Core\Http\Contracts\ArgumentResolverInterface;
use Concept\Core\Http\Routing\Resolvers\RouteParameterArgumentResolver;
use Concept\Core\Http\Routing\Resolvers\ServerRequestArgumentResolver;
use Concept\Core\Providers\Http\HttpKernelServiceProvider;
use Concept\Extensions\CastingValinor\CastingServiceProvider;
use Concept\Extensions\CastingValinor\Contracts\CasterInterface;
use Concept\Extensions\CastingValinor\Routing\TypedRouteParameterArgumentResolver;
use Concept\Extensions\Http\HttpServiceProvider;
use Concept\Stack\Options\HttpOptions;
use League\Container\DefinitionContainerInterface;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;

final class HttpStackProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    public function __construct(
        private readonly HttpOptions $options,
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

        $container->addServiceProvider(new CastingServiceProvider(
            cacheDirectory: $this->options->cacheDirectory(),
            transformerClasses: $this->options->transformerClasses(),
            debug: $this->options->debug(),
        ));

        $container->addServiceProvider(new HttpKernelServiceProvider(
            routePaths: $this->options->routes(),
            resolvers: $this->getArgumentResolvers($container),
            interceptors: $this->options->interceptors(),
            notFoundMiddleware: $this->options->notFoundMiddleware(),
        ));

        $container->addServiceProvider(new HttpServiceProvider());
    }

    /**
     * @return list<ArgumentResolverInterface>
     */
    private function getArgumentResolvers(DefinitionContainerInterface $container): array
    {
        return [
            new ServerRequestArgumentResolver(),
            new TypedRouteParameterArgumentResolver(
                fn(): CasterInterface => ContainerDependency::get($container, CasterInterface::class),
            ),
            new RouteParameterArgumentResolver(),
        ];
    }
}
