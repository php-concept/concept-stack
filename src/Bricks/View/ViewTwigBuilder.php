<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\View;

use Concept\Stack\Capability\Capability;
use Concept\Stack\Exceptions\InvalidCapabilityOptionsException;

/**
 * Nested under ViewBuilder. end() returns ViewBuilder (not StackBuilder).
 */
final class ViewTwigBuilder
{
    public function __construct(
        private readonly ViewBuilder $parent,
        private readonly ViewOptions $options,
    ) {}

    /**
     * Absolute path to the primary Twig templates root (FilesystemLoader).
     */
    public function viewsPath(string $viewsPath): self
    {
        $this->options->setTwigViewsPath($viewsPath);

        return $this;
    }

    /**
     * Absolute Twig cache directory. Empty + debug=false means Twig default behaviour.
     */
    public function cacheDir(string $cacheDir): self
    {
        $this->options->setTwigCacheDir($cacheDir);

        return $this;
    }

    public function debug(bool $debug = true): self
    {
        $this->options->setTwigDebug($debug);

        return $this;
    }

    public function end(): ViewBuilder
    {
        if ($this->options->twigViewsPath() === '') {
            throw InvalidCapabilityOptionsException::missingOption(Capability::VIEW, 'withTwig()->viewsPath');
        }

        return $this->parent;
    }
}
