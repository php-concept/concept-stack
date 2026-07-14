<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Casting;

final class CastingBuilder
{
    public function __construct(
        private readonly CastingOptions $options,
    ) {}

    public function cacheDir(string $cacheDirectory): self
    {
        $this->options->setCacheDirectory($cacheDirectory);

        return $this;
    }

    /**
     * @param list<class-string> $transformerClasses
     */
    public function transformers(array $transformerClasses): self
    {
        $this->options->setTransformerClasses($transformerClasses);

        return $this;
    }

    public function debug(bool $debug = true): self
    {
        $this->options->setDebug($debug);

        return $this;
    }
}
