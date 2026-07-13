<?php declare(strict_types=1);

namespace Concept\Stack\Options;

use Concept\Extensions\DataMasker\Contracts\DataMaskerRuleInterface;

final class MaskingOptions
{
    /** @var array<string, string> */
    private array $patterns = [];

    /** @var list<string> */
    private array $keyPatterns = [];

    /** @var list<class-string<DataMaskerRuleInterface>> */
    private array $rules = [];

    /**
     * @return array<string, string>
     */
    public function patterns(): array
    {
        return $this->patterns;
    }

    /**
     * @param array<string, string> $patterns
     */
    public function setPatterns(array $patterns): void
    {
        $this->patterns = $patterns;
    }

    /**
     * @return list<string>
     */
    public function keyPatterns(): array
    {
        return $this->keyPatterns;
    }

    /**
     * @param list<string> $keyPatterns
     */
    public function setKeyPatterns(array $keyPatterns): void
    {
        $this->keyPatterns = $keyPatterns;
    }

    /**
     * @return list<class-string<DataMaskerRuleInterface>>
     */
    public function rules(): array
    {
        return $this->rules;
    }

    /**
     * @param list<class-string<DataMaskerRuleInterface>> $rules
     */
    public function setRules(array $rules): void
    {
        $this->rules = $rules;
    }
}
