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

    /** @var array<string|class-string, mixed> */
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
     * @param string $parameter
     * @param mixed $value
     * @return $this
     */
    public function addArgument(string $parameter, mixed $value): self
    {
        $this->arguments[$parameter] = $value;

        return $this;
    }

    /**
     * @param array<string|class-string, mixed> $arguments
     * @return $this
     */
    public function addArguments(array $arguments): self
    {
        $parameters = array_keys($arguments);
        foreach ($parameters as $parameter) {
            if (!is_string($parameter)) {
                throw new ContainerException(
                    "Invalid parameter name provided (" . $parameter . "). Parameter must be a string."
                );
            }
        }

        $this->arguments = array_merge($this->arguments, array_combine($parameters, array_values($arguments)));

        return $this;
    }

    /**
     * @return array<string|class-string, mixed>
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }
}
