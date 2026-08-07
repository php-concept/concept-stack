<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\View;

use Concept\Stack\Capability\Capability;
use Concept\Stack\Exceptions\InvalidCapabilityOptionsException;

final class ViewOptions
{
    public const string ENGINE_TWIG = 'twig';
    public const string ENGINE_PLATES = 'plates';

    /** @var array<string, string> namespace => absolute filesystem path */
    private array $paths = [];

    /** @var list<class-string> */
    private array $extensions = [];

    /** @var array<string, string> */
    private array $routeNamespace = [];

    private ?string $engine = null;

    private string $twigViewsPath = '';

    private ?string $twigCacheDir = null;

    private bool $twigDebug = false;

    private ?string $twigDefaultExtension = null;

    private string $platesViewsPath = '';

    private ?string $platesDefaultExtension = null;

    /**
     * @return array<string, string>
     */
    public function paths(): array
    {
        return $this->paths;
    }

    /**
     * @param array<string, string> $paths namespace => absolute filesystem path
     */
    public function setPaths(array $paths): void
    {
        $this->paths = $paths;
    }

    /**
     * @return list<class-string>
     */
    public function extensions(): array
    {
        return $this->extensions;
    }

    /**
     * @param list<class-string> $extensions
     */
    public function setExtensions(array $extensions): void
    {
        $this->extensions = $extensions;
    }

    /**
     * @return array<string, string>
     */
    public function routeNamespaces(): array
    {
        return $this->routeNamespace;
    }

    /**
     * @param array<string, string> $routeNamespaces
     */
    public function setRouteNamespaces(array $routeNamespaces): void
    {
        $this->routeNamespace = $routeNamespaces;
    }

    public function engine(): ?string
    {
        return $this->engine;
    }

    public function setEngine(string $engine): void
    {
        $this->engine = $engine;
    }

    public function twigViewsPath(): string
    {
        return $this->twigViewsPath;
    }

    public function setTwigViewsPath(string $twigViewsPath): void
    {
        $this->twigViewsPath = $twigViewsPath;
    }

    public function twigCacheDir(): ?string
    {
        return $this->twigCacheDir;
    }

    public function setTwigCacheDir(?string $twigCacheDir): void
    {
        $this->twigCacheDir = $twigCacheDir;
    }

    public function twigDebug(): bool
    {
        return $this->twigDebug;
    }

    public function setTwigDebug(bool $twigDebug): void
    {
        $this->twigDebug = $twigDebug;
    }

    public function twigDefaultExtension(): ?string
    {
        return $this->twigDefaultExtension;
    }

    public function setTwigDefaultExtension(string $twigDefaultExtension): void
    {
        $this->twigDefaultExtension = $twigDefaultExtension;
    }

    public function platesViewsPath(): string
    {
        return $this->platesViewsPath;
    }

    public function setPlatesViewsPath(string $platesViewsPath): void
    {
        $this->platesViewsPath = $platesViewsPath;
    }

    public function platesDefaultExtension(): ?string
    {
        return $this->platesDefaultExtension;
    }

    public function setPlatesDefaultExtension(string $platesDefaultExtension): void
    {
        $this->platesDefaultExtension = $platesDefaultExtension;
    }

    public function assertValid(): void
    {
        if ($this->engine === null) {
            throw InvalidCapabilityOptionsException::missingOption(Capability::VIEW, 'withTwig()/withPlates()');
        }

        if ($this->engine === self::ENGINE_TWIG && $this->twigViewsPath === '') {
            throw InvalidCapabilityOptionsException::missingOption(Capability::VIEW, 'withTwig()->setViewsPath()');
        }

        if ($this->engine === self::ENGINE_PLATES && $this->platesViewsPath === '') {
            throw InvalidCapabilityOptionsException::missingOption(Capability::VIEW, 'withPlates()->setViewsPath()');
        }
    }
}
