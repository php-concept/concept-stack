<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\View;

final class ViewTwigBuilder
{
    public function __construct(
        private readonly ViewOptions $options,
    ) {}

    /**
     * Absolute path to the primary Twig templates root (FilesystemLoader).
     */
    public function setViewsPath(string $viewsPath): self
    {
        $this->options->setTwigViewsPath($viewsPath);

        return $this;
    }

    /**
     * Absolute Twig cache directory. Omit (null default) → no filesystem cache.
     */
    public function setCacheDir(string $cacheDir): self
    {
        $this->options->setTwigCacheDir($cacheDir);

        return $this;
    }

    public function setDebug(bool $debug = true): self
    {
        $this->options->setTwigDebug($debug);

        return $this;
    }

    /**
     * File suffix appended when the template name has none (default `.twig`).
     */
    public function setDefaultExtension(string $defaultExtension): self
    {
        $this->options->setTwigDefaultExtension($defaultExtension);

        return $this;
    }
}
