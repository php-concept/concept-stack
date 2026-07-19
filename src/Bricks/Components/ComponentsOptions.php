<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Components;

use Concept\Extensions\Components\Contracts\ComponentInterface;

final class ComponentsOptions
{
    /** @var list<class-string<ComponentInterface>> */
    private array $componentClasses = [];

    private bool $database = false;
    private bool $console = false;
    private bool $http = false;
    private bool $view = false;

    /**
     * @return list<class-string<ComponentInterface>>
     */
    public function componentClasses(): array
    {
        return $this->componentClasses;
    }

    /**
     * @param list<class-string<ComponentInterface>> $componentClasses
     */
    public function setComponentClasses(array $componentClasses): void
    {
        $this->componentClasses = $componentClasses;
    }

    public function database(): bool
    {
        return $this->database;
    }

    public function setDatabase(bool $database): void
    {
        $this->database = $database;
    }

    public function console(): bool
    {
        return $this->console;
    }

    public function setConsole(bool $console): void
    {
        $this->console = $console;
    }

    public function http(): bool
    {
        return $this->http;
    }

    public function setHttp(bool $http): void
    {
        $this->http = $http;
    }

    public function view(): bool
    {
        return $this->view;
    }

    public function setView(bool $view): void
    {
        $this->view = $view;
    }
}
