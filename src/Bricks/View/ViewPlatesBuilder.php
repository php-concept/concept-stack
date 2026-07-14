<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\View;

final class ViewPlatesBuilder
{
    public function __construct(
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
}
