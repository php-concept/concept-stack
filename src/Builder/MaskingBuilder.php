<?php declare(strict_types=1);

namespace Concept\Stack\Builder;

use Concept\Extensions\DataMasker\Contracts\DataMaskerRuleInterface;
use Concept\Stack\Builder\Contracts\StackCapabilityBuilder;
use Concept\Stack\Capability\Capability;
use Concept\Stack\Options\MaskingOptions;
use Concept\Stack\Providers\MaskingStackProvider;

final class MaskingBuilder implements StackCapabilityBuilder
{
    public function __construct(
        private readonly StackBuilder $parent,
        private readonly MaskingOptions $options,
    ) {}

    /**
     * @param array<string, string> $patterns
     */
    public function patterns(array $patterns): self
    {
        $this->options->setPatterns($patterns);

        return $this;
    }

    /**
     * @param list<string> $keyPatterns
     */
    public function keyPatterns(array $keyPatterns): self
    {
        $this->options->setKeyPatterns($keyPatterns);

        return $this;
    }

    /**
     * @param list<class-string<DataMaskerRuleInterface>> $rules
     */
    public function rules(array $rules): self
    {
        $this->options->setRules($rules);

        return $this;
    }

    public function end(): StackBuilder
    {
        $this->parent->registerCapability(
            Capability::MASKING,
            new MaskingStackProvider($this->options),
        );

        return $this->parent;
    }
}
