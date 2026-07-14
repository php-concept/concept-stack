<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Session;

use SessionHandlerInterface;

final class SessionOptions
{
    /** @var array<string, mixed> */
    private array $sessionOptions = [];

    private ?SessionHandlerInterface $handler = null;

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

    public function handler(): ?SessionHandlerInterface
    {
        return $this->handler;
    }

    public function setHandler(SessionHandlerInterface $handler): void
    {
        $this->handler = $handler;
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
