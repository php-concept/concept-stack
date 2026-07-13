<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Casting;

final class CastingOptions
{
    private string $cacheDirectory = '';

    /** @var list<class-string> */
    private array $transformerClasses = [];

    private bool $debug = false;

    public function cacheDirectory(): string
    {
        return $this->cacheDirectory;
    }

    public function setCacheDirectory(string $cacheDirectory): void
    {
        $this->cacheDirectory = $cacheDirectory;
    }

    /**
     * @return list<class-string>
     */
    public function transformerClasses(): array
    {
        return $this->transformerClasses;
    }

    /**
     * @param list<class-string> $transformerClasses
     */
    public function setTransformerClasses(array $transformerClasses): void
    {
        $this->transformerClasses = $transformerClasses;
    }

    public function debug(): bool
    {
        return $this->debug;
    }

    public function setDebug(bool $debug): void
    {
        $this->debug = $debug;
    }
}
