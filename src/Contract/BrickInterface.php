<?php declare(strict_types=1);

namespace Concept\Stack\Contract;

use League\Container\ServiceProvider\ServiceProviderInterface;

/**
 * Stack brick — one capability contributed to ConceptStack.
 *
 * First-party packages add a thin *Brick entry that wraps Builder / Options / StackProvider.
 * Register via StackBuilder::add().
 *
 * Options validation stays inside the package (typically *StackProvider), not on this contract.
 */
interface BrickInterface
{
    /**
     * Capability name for dependency ordering (unique per stack).
     */
    public function name(): string;

    /**
     * Other capability names that must be present (base deps).
     * Opt-in deps (withMasking, withFormRequests, …) are added via StackBuilder::require()
     * after bind — see BindableBrickInterface.
     *
     * @return list<string>
     */
    public function requires(): array;

    /**
     * Service providers contributed by this brick (registration order).
     *
     * @return list<ServiceProviderInterface>
     */
    public function providers(): array;
}
