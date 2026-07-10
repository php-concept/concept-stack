<?php declare(strict_types=1);

namespace Concept\Stack\Options;

use RuntimeException;

final class StackLayerOptions
{
    /**
     * @param array<string, mixed> $values
     */
    public function __construct(
        private readonly array $values = [],
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->values;
    }

    public function get(string $key): mixed
    {
        return $this->values[$key] ?? null;
    }

    /**
     * @return list<string>|null
     */
    public function stringListOrNull(string $key): ?array
    {
        $value = $this->get($key);
        if ($value === null) {
            return null;
        }

        if (!is_array($value) || !array_is_list($value)) {
            throw new RuntimeException(sprintf('Stack option "%s" must be a list of strings or null.', $key));
        }

        foreach ($value as $item) {
            if (!is_string($item)) {
                throw new RuntimeException(sprintf('Stack option "%s" must be a list of strings or null.', $key));
            }
        }

        return $value;
    }
}
