<?php declare(strict_types=1);

namespace Concept\Stack\Support;

use Closure;
use Concept\Core\Container\ContainerDependency;
use Psr\Container\ContainerInterface;

/**
 * Glue-only helper for optional cross-extension dependencies. Produces the lazy
 * factory closures that extension providers accept (e.g. dataMaskerFactory,
 * casterFactory), resolving to null when the dependency is not registered.
 */
final class OptionalDependency
{
    /**
     * @template T of object
     * @param class-string<T> $id
     * @return Closure(): ?T
     */
    public static function factory(ContainerInterface $container, string $id): Closure
    {
        return static function() use ($container, $id): ?object {
            if (!$container->has($id)) {
                return null;
            }

            return ContainerDependency::get($container, $id);
        };
    }
}
