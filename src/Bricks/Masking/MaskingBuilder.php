<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Masking;

use Concept\Extensions\DataMasker\Contracts\DataMaskerRuleInterface;

final class MaskingBuilder
{
    public function __construct(
        private readonly MaskingOptions $options,
    ) {}

    /**
     * @param array<string, string> $patterns
     */
    public function setPatterns(array $patterns): self
    {
        $this->options->setPatterns($patterns);

        return $this;
    }

    /**
     * @param list<string> $keyPatterns
     */
    public function setKeyPatterns(array $keyPatterns): self
    {
        $this->options->setKeyPatterns($keyPatterns);

        return $this;
    }

    /**
     * @param list<class-string<DataMaskerRuleInterface>> $rules
     */
    public function setRules(array $rules): self
    {
        $this->options->setRules($rules);

        return $this;
    }
}
