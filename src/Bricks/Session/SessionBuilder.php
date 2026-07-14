<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Session;

use SessionHandlerInterface;

final class SessionBuilder
{
    public function __construct(
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
}
