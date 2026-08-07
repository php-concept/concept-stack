<?php declare(strict_types=1);

namespace Concept\Stack\Capability;

use Concept\Stack\Exceptions\InvalidCapabilityOptionsException;
use Concept\Stack\Exceptions\MissingCapabilityDependencyException;

/**
 * Tracks enabled stack capabilities and validates dependencies between them.
 */
final class CapabilityRegistry
{
    /** @var array<string, list<string>> capability name => required capability names */
    private array $capabilities = [];

    /**
     * @param list<string> $requires
     */
    public function register(string $name, array $requires = []): void
    {
        if ($this->has($name)) {
            throw InvalidCapabilityOptionsException::alreadyEnabled($name);
        }

        $this->capabilities[$name] = $requires;
    }

    public function require(string $name, string $dependency): void
    {
        if (!$this->has($name)) {
            throw InvalidCapabilityOptionsException::missingOption($name, 'enable via add*() first');
        }

        if (!in_array($dependency, $this->capabilities[$name], true)) {
            $this->capabilities[$name][] = $dependency;
        }
    }

    public function has(string $name): bool
    {
        return isset($this->capabilities[$name]);
    }

    public function assertDependencies(): void
    {
        foreach ($this->capabilities as $name => $requires) {
            foreach ($requires as $dependency) {
                if (!$this->has($dependency)) {
                    throw MissingCapabilityDependencyException::forDependency($name, $dependency);
                }
            }
        }
    }

    /**
     * Returns a stable topological order: dependencies before their consumers,
     * preserving registration order where no dependency constrains it.
     *
     * @return list<string>
     */
    public function orderedNames(): array
    {
        $this->assertDependencies();

        $ordered = [];
        $visiting = [];
        $visited = [];

        foreach (array_keys($this->capabilities) as $name) {
            $this->visit($name, $visiting, $visited, $ordered);
        }

        return $ordered;
    }

    /**
     * @param array<string, true> $visiting
     * @param array<string, true> $visited
     * @param list<string> $ordered
     */
    private function visit(string $name, array &$visiting, array &$visited, array &$ordered): void
    {
        if (isset($visited[$name])) {
            return;
        }

        if (isset($visiting[$name])) {
            throw InvalidCapabilityOptionsException::circularDependency($name);
        }

        $visiting[$name] = true;

        foreach ($this->capabilities[$name] as $dependency) {
            $this->visit($dependency, $visiting, $visited, $ordered);
        }

        unset($visiting[$name]);
        $visited[$name] = true;
        $ordered[] = $name;
    }
}
