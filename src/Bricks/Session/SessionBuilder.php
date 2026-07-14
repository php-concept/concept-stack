<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Session;

use Concept\Stack\Builder\Contracts\StackCapabilityBuilder;
use Concept\Stack\Builder\StackBuilder;
use Concept\Stack\Capability\Capability;
use SessionHandlerInterface;

final class SessionBuilder implements StackCapabilityBuilder
{
    public function __construct(
        private readonly StackBuilder $parent,
        private readonly SessionOptions $options,
    ) {}

    /**
     * @param array<string, mixed> $options Native session options (cookie_*, use_strict_mode, …)
     */
    public function options(array $options): self
    {
        $this->options->setSessionOptions($options);

        return $this;
    }

    /**
     * Explicit session handler. When omitted, NativeFileSessionHandler() with PHP defaults is used.
     * Stack does not resolve storage paths — pass a ready handler from app glue if needed.
     */
    public function handler(SessionHandlerInterface $handler): self
    {
        $this->options->setHandler($handler);

        return $this;
    }

    /**
     * Opt-in CSRF token manager (CsrfServiceProvider). Middleware stays in routes.
     */
    public function withCsrf(): self
    {
        $this->options->setCsrf(true);

        return $this;
    }

    public function end(): StackBuilder
    {
        $this->parent->registerCapability(
            Capability::SESSION,
            new SessionStackProvider($this->options),
        );

        return $this->parent;
    }
}
