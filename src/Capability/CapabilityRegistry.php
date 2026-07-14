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
            throw InvalidCapabilityOptionsException::missingOption($name, 'enable via with*() first');
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
}
