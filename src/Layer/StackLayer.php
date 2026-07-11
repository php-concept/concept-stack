<?php declare(strict_types=1);

namespace Concept\Stack\Layer;

final class StackLayer
{
    public const string FOUNDATION = 'foundation';
    public const string LOGGING = 'logging';
    public const string TELEMETRY = 'telemetry';
    public const string VALIDATION = 'validation';
    public const string DATABASE = 'database';
    public const string SESSION = 'session';
    public const string HTTP = 'http';
    public const string CONSOLE = 'console';
    public const string VIEW = 'view';
    public const string TWIG_ERRORS = 'twig-errors';
    public const string JSON_ERRORS = 'json-errors';
    public const string COMPONENTS = 'components';
    public const string RUNTIME = 'runtime';
}
