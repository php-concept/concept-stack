<?php declare(strict_types=1);

namespace Concept\Stack\Exceptions;

final class InvalidCapabilityOptionsException extends ConceptStackException
{
    private const string ERR_MISSING = 'Capability "%s" requires option "%s" to be set.';

    public static function missingOption(string $capability, string $option): self
    {
        return new self(sprintf(self::ERR_MISSING, $capability, $option));
    }
}
