<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Casting;

use Concept\Extensions\CastingValinor\CastingServiceProvider;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;

final class CastingStackProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    public function __construct(
        private readonly CastingOptions $options,
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
        $this->getContainer()->addServiceProvider(new CastingServiceProvider(
            transformerClasses: $this->options->transformerClasses(),
            cacheDirectory: $this->options->cacheDirectory(),
            debug: $this->options->debug(),
        ));
    }
}
