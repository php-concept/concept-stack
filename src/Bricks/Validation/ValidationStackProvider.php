<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Validation;

use Concept\Core\Container\ContainerDependency;
use Concept\Extensions\CastingValinor\Contracts\CasterInterface;
use Concept\Extensions\DataMasker\Contracts\DataMaskerInterface;
use Concept\Extensions\FormRequest\FormRequestServiceProvider;
use Concept\Extensions\ValidationRakit\Contracts\ValidatorInterface;
use Concept\Extensions\ValidationRakit\ValidationLogger;
use Concept\Extensions\ValidationRakit\ValidationServiceProvider;
use Concept\Stack\Support\OptionalDependency;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;

final class ValidationStackProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    public function __construct(
        private readonly ValidationOptions $options,
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

        $container->addServiceProvider(new ValidationServiceProvider(
            customRules: $this->options->customRules(),
            logEnabled: $this->options->logEnabled(),
            logFilePath: $this->options->logFilePath(),
            logMaxFiles: $this->options->logMaxFiles(),
            dataMaskerFactory: OptionalDependency::factory($container, DataMaskerInterface::class),
        ));

        $container->addServiceProvider(new FormRequestServiceProvider(
            validatorFactory: fn(): ValidatorInterface => ContainerDependency::get($container, ValidatorInterface::class),
            globalExcept: $this->options->globalExcept(),
            casterFactory: OptionalDependency::factory($container, CasterInterface::class),
            validationLoggerFactory: OptionalDependency::factory($container, ValidationLogger::class),
        ));
    }
}
