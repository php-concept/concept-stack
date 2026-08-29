<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Session;

use Closure;
use SessionHandlerInterface;

final class SessionBuilder
{
    public function __construct(
        private readonly SessionOptions $options,
    ) {}

    /**
     * @param array<string, mixed> $options Native session options (cookie_*, use_strict_mode, …)
     */
    public function setOptions(array $options): self
    {
        $this->options->setSessionOptions($options);

        return $this;
    }

    /**
     * Session handler factory. When omitted, NativeFileSessionHandler() with PHP defaults is used.
     * Stack does not resolve storage paths — pass a factory from app glue (file, PDO, custom, …).
     *
     * @param Closure(): SessionHandlerInterface $handlerFactory
     */
    public function setHandlerFactory(Closure $handlerFactory): self
    {
        $this->options->setHandlerFactory($handlerFactory);

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
