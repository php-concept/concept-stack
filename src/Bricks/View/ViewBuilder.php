<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\View;

use Concept\Stack\Builder\Contracts\StackCapabilityBuilder;
use Concept\Stack\Builder\StackBuilder;
use Concept\Stack\Capability\Capability;
use Concept\Stack\Exceptions\InvalidCapabilityOptionsException;

final class ViewBuilder implements StackCapabilityBuilder
{
    private const string ERR_ENGINE_XOR = 'Capability "view" accepts only one engine: withTwig() or withPlates().';

    public function __construct(
        private readonly StackBuilder $parent,
        private readonly ViewOptions $options,
    ) {}

    /**
     * Named template paths (namespace => absolute filesystem path).
     *
     * @param array<string, string> $paths
     */
    public function paths(array $paths): self
    {
        $this->options->setPaths($paths);

        return $this;
    }

    /**
     * Engine extension class names (resolved from the container).
     *
     * @param list<class-string> $extensions
     */
    public function extensions(array $extensions): self
    {
        $this->options->setExtensions($extensions);

        return $this;
    }

    /**
     * @param array<string, string> $routeNamespace
     */
    public function routeNamespace(array $routeNamespace): self
    {
        $this->options->setRouteNamespace($routeNamespace);

        return $this;
    }

    /**
     * Opt-in Twig engine (ViewInterface). XOR with withPlates().
     */
    public function withTwig(): ViewTwigBuilder
    {
        $this->assertEngineUnsetOr(ViewOptions::ENGINE_TWIG);
        $this->options->setEngine(ViewOptions::ENGINE_TWIG);

        return new ViewTwigBuilder($this, $this->options);
    }

    /**
     * Opt-in Plates engine (ViewInterface). XOR with withTwig().
     */
    public function withPlates(): ViewPlatesBuilder
    {
        $this->assertEngineUnsetOr(ViewOptions::ENGINE_PLATES);
        $this->options->setEngine(ViewOptions::ENGINE_PLATES);

        return new ViewPlatesBuilder($this, $this->options);
    }

    public function end(): StackBuilder
    {
        if ($this->options->engine() === null) {
            throw InvalidCapabilityOptionsException::missingOption(Capability::VIEW, 'withTwig()/withPlates()');
        }

        $this->parent->registerCapability(
            Capability::VIEW,
            new ViewStackProvider($this->options),
            [Capability::HTTP],
        );

        return $this->parent;
    }

    private function assertEngineUnsetOr(string $engine): void
    {
        $current = $this->options->engine();
        if ($current !== null && $current !== $engine) {
            throw new InvalidCapabilityOptionsException(self::ERR_ENGINE_XOR);
        }
    }
}
