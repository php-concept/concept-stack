<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Session;

use Closure;
use SessionHandlerInterface;

final class SessionOptions
{
    /** @var array<string, mixed> */
    private array $sessionOptions = [];

    /** @var Closure(): SessionHandlerInterface|null */
    private ?Closure $handlerFactory = null;

    private bool $csrf = false;

    /**
     * @return array<string, mixed>
     */
    public function sessionOptions(): array
    {
        return $this->sessionOptions;
    }

    /**
     * @param array<string, mixed> $sessionOptions
     */
    public function setSessionOptions(array $sessionOptions): void
    {
        $this->sessionOptions = $sessionOptions;
    }

    /**
     * @return Closure(): SessionHandlerInterface|null
     */
    public function handlerFactory(): ?Closure
    {
        return $this->handlerFactory;
    }

    /**
     * @param Closure(): SessionHandlerInterface $handlerFactory
     */
    public function setHandlerFactory(Closure $handlerFactory): void
    {
        $this->handlerFactory = $handlerFactory;
    }

    public function csrf(): bool
    {
        return $this->csrf;
    }

    public function setCsrf(bool $csrf): void
    {
        $this->csrf = $csrf;
    }
}
