<?php declare(strict_types=1);

namespace Concept\Stack\Capability;

/**
 * Canonical capability names used for dependency checks between stack features.
 */
final class Capability
{
    public const string LOGGING = 'logging';
    public const string TELEMETRY = 'telemetry';
    public const string VALIDATION = 'validation';
    public const string FLASH_VALIDATION = 'flash-validation';
    public const string DATABASE = 'database';
    public const string CASTING = 'casting';
    public const string SESSION = 'session';
    public const string CSRF = 'csrf';
    public const string HTTP = 'http';
    public const string CONSOLE = 'console';
    public const string VIEW = 'view';
    public const string ERROR_HANDLING = 'error-handling';
}
