<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Http;

use Concept\Core\Container\ContainerDependency;
use Concept\Core\Http\Contracts\ArgumentResolverInterface;
use Concept\Core\Http\Routing\Resolvers\RouteParameterArgumentResolver;
use Concept\Core\Http\Routing\Resolvers\ServerRequestArgumentResolver;
use Concept\Core\Providers\Http\HttpKernelServiceProvider;
use Concept\Extensions\CastingValinor\Contracts\CasterInterface;
use Concept\Extensions\CastingValinor\Routing\TypedRouteParameterArgumentResolver;
use Concept\Extensions\FormRequest\Contracts\FormRequestFactoryInterface;
use Concept\Extensions\FormRequest\Routing\FormRequestArgumentResolver;
use Concept\Extensions\Http\HttpServiceProvider;
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
        $resolvers = [];

        if ($this->options->formRequests()) {
            $resolvers[] = new FormRequestArgumentResolver(
                formRequestFactory: fn(): FormRequestFactoryInterface => ContainerDependency::get(
                    $container,
                    FormRequestFactoryInterface::class,
                ),
                container: $container,
            );
        }

        $resolvers[] = new ServerRequestArgumentResolver();

        if ($this->options->typedRouteParameters()) {
            $resolvers[] = new TypedRouteParameterArgumentResolver(
                fn(): CasterInterface => ContainerDependency::get($container, CasterInterface::class),
            );
        }

        $resolvers[] = new RouteParameterArgumentResolver();

        return $resolvers;
    }
}
