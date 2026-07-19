<?php declare(strict_types=1);

namespace Concept\Stack\Exceptions;

final class InvalidCapabilityOptionsException extends ConceptStackException
{
    private const string ERR_MISSING = 'Capability "%s" requires option "%s" to be set.';

    private const string ERR_MISSING_HANDLERS = 'Capability "%s" requires at least one handler (toRotatingFile, toStderr, toHandler).';

    private const string ERR_ALREADY_ENABLED = 'Capability "%s" is already enabled.';
    private const string ERR_CIRCULAR_DEPENDENCY = 'Circular capability dependency detected at "%s".';

    public static function missingOption(string $capability, string $option): self
    {
        return new self(sprintf(self::ERR_MISSING, $capability, $option));
    }

    public static function missingHandlers(string $capability): self
    {
        return new self(sprintf(self::ERR_MISSING_HANDLERS, $capability));
    }

    public static function alreadyEnabled(string $capability): self
    {
        return new self(sprintf(self::ERR_ALREADY_ENABLED, $capability));
    }

    public static function circularDependency(string $capability): self
    {
        return new self(sprintf(self::ERR_CIRCULAR_DEPENDENCY, $capability));
    }
}
