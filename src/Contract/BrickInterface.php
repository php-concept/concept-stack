<?php declare(strict_types=1);

namespace Concept\Stack\Contract;

use League\Container\ServiceProvider\ServiceProviderInterface;

/**
 * App / third-party stack brick. Register via StackBuilder::addCustom().
 */
interface BrickInterface
{
    /**
     * Capability name for dependency ordering (unique per stack).
     */
    public function name(): string;

    /**
     * Other capability names that must be enabled first.
     *
     * @return list<string>
     */
    public function requires(): array;

    /**
     * Service providers contributed by this brick (in registration order).
     *
     * @return list<ServiceProviderInterface>
     */
    public function providers(): array;

    /**
     * Fail fast before providers() are collected.
     */
    public function assertValid(): void;
}
