<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Masking;

use Concept\Extensions\DataMasker\DataMaskerServiceProvider;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;

final class MaskingStackProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    public function __construct(
        private readonly MaskingOptions $options,
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
        $this->getContainer()->addServiceProvider(new DataMaskerServiceProvider(
            patterns: $this->options->patterns(),
            keyPatterns: $this->options->keyPatterns(),
            rules: $this->options->rules(),
        ));
    }
}
