<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\View;

use Concept\Stack\Capability\Capability;
use Concept\Stack\Exceptions\InvalidCapabilityOptionsException;

/**
 * Nested under ViewBuilder. end() returns ViewBuilder (not StackBuilder).
 */
final class ViewPlatesBuilder
{
    public function __construct(
        private readonly ViewBuilder $parent,
        private readonly ViewOptions $options,
    ) {}

    /**
     * Absolute path to the primary Plates templates root.
     */
    public function viewsPath(string $viewsPath): self
    {
        $this->options->setPlatesViewsPath($viewsPath);

        return $this;
    }

    public function end(): ViewBuilder
    {
        if ($this->options->platesViewsPath() === '') {
            throw InvalidCapabilityOptionsException::missingOption(Capability::VIEW, 'withPlates()->viewsPath');
        }

        return $this->parent;
    }
}
