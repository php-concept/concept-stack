<?php declare(strict_types=1);

namespace Concept\Stack;

use Concept\Stack\Builder\ConceptStackBuilder;
use Concept\Stack\Provider\StackProviderRegistry;

final class ConceptStack
{
    public static function custom(string $root, StackProviderRegistry $providers): ConceptStackBuilder
    {
        return new ConceptStackBuilder($root, $providers);
    }

    /**
     * @param list<string> $routePaths
     */
    public static function minimal(string $root, StackProviderRegistry $providers, array $routePaths): ConceptStackBuilder
    {
        return self::custom($root, $providers)
            ->withHttp(routePaths: $routePaths, minimal: true);
    }

    /**
     * @param list<string> $routePaths
     */
    public static function api(string $root, StackProviderRegistry $providers, array $routePaths): ConceptStackBuilder
    {
        return self::custom($root, $providers)
            ->withLogging()
            ->withTelemetry()
            ->withValidation()
            ->withDatabase()
            ->withHttp(routePaths: $routePaths)
            ->withConsole()
            ->withJsonErrors()
            ->withRuntime();
    }

    public static function web(string $root, StackProviderRegistry $providers): ConceptStackBuilder
    {
        return self::custom($root, $providers)
            ->withLogging()
            ->withTelemetry()
            ->withFlashValidation()
            ->withHttp()
            ->withConsole()
            ->withTwig()
            ->withPretty404()
            ->withRuntime();
    }

    public static function full(string $root, StackProviderRegistry $providers): ConceptStackBuilder
    {
        return self::web($root, $providers)
            ->withDatabase()
            ->withComponents();
    }
}
