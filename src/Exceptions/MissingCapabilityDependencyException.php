<?php declare(strict_types=1);

namespace Concept\Stack\Exceptions;

final class MissingCapabilityDependencyException extends ConceptStackException
{
    private const string ERR_MISSING = 'Capability "%s" requires "%s" to be enabled.';

    public static function forDependency(string $capability, string $dependency): self
    {
        return new self(sprintf(self::ERR_MISSING, $capability, $dependency));
    }
}
