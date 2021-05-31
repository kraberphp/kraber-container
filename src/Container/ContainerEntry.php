<?php

declare(strict_types=1);

namespace Kraber\Container;

/**
 * Class ContainerEntry
 *
 * @template C of object
 */
class ContainerEntry
{
    /** @var class-string<C> */
    private string $identifier;

    /** @var bool */
    private bool $shared = false;

    /** @var mixed[] */
    private array $arguments = [];

    /** @var array<string, mixed[]> */
    private array $calls = [];

    /**
     * ContainerEntry constructor.
     *
     * @param class-string<C> $identifier
     * @param bool $shared
     */
    public function __construct(
        string $identifier,
        bool $shared = false
    ) {
        $this->identifier = $identifier;
        $this->shared = $shared;
    }

    /**
     * @return class-string<C>
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return bool
     */
    public function isShared(): bool
    {
        return $this->shared;
    }

    /**
     * @param bool $shared
     *
     * @return $this
     */
    public function setShared(bool $shared = true): self
    {
        $this->shared = $shared;

        return $this;
    }

    /**
     * @param mixed $arg
     *
     * @return $this
     */
    public function addArgument(mixed $arg): self
    {
        $this->arguments[] = $arg;

        return $this;
    }

    /**
     * @param mixed[] $args
     *
     * @return $this
     */
    public function addArguments(mixed ...$args): self
    {
        $this->arguments = array_merge($this->arguments, array_values($args));

        return $this;
    }
}
