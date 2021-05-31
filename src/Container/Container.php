<?php

declare(strict_types=1);

namespace Kraber\Container;

use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionParameter;
use ReflectionUnionType;
use ReflectionNamedType;
use ReflectionException;
use Exception;

class Container implements ContainerInterface
{
    /** @var array<class-string, class-string> */
    private array $interfaces = [];

    /**
     * @param class-string $interface
     * @param class-string $class
     * @throws ContainerException If provided interface is invalid or class does not implement interface.
     */
    public function bind(string $interface, string $class): void
    {
        if (!(interface_exists($interface) && is_subclass_of($class, $interface))) {
            throw new ContainerException(
                "Could not bind interface '" . $interface . "' to concrete class '" . $class . "'." .
                "Please make sure interface exists and class implements it."
            );
        }

        $this->interfaces[$interface] = $class;
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @template T of object
     *
     * @param class-string<T> $id Identifier of the entry to look for.
     *
     * @return T Entry.
     * @throws NotFoundException  No entry was found for **this** identifier.
     * @throws ContainerException Error while retrieving the entry.
     * @throws ReflectionException
     */
    public function get(string $id): object
    {
        if (!$this->has($id)) {
            throw new NotFoundException("Unable to found concrete implementation for '" . $id . "'.");
        }

        /** @var T */
        return $this->resolve($this->interfaces[$id]);
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param class-string $id Identifier of the entry to look for.
     * @return bool
     */
    public function has(string $id): bool
    {
        return isset($this->interfaces[$id]);
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $concrete
     *
     * @return T
     * @throws ContainerException Error while resolving dependencies.
     * @throws ReflectionException If an error occurred during reflection.
     * @throws NotFoundException
     */
    private function resolve(string $concrete): object
    {
        $reflectionClass = new ReflectionClass($concrete);
        if (!$reflectionClass->isInstantiable()) {
            throw new ContainerException("Class '" . $concrete . "' is not an instantiable.");
        }

        $concreteCtor = $reflectionClass->getConstructor();
        if ($concreteCtor === null) {
            /** @var T */
            return $reflectionClass->newInstance();
        }

        try {
            $dependencies = $this->resolveParameters($concreteCtor->getParameters());
        } catch (ContainerException $e) {
            throw new ContainerException("Class '" . $concrete . "' unable to resolve dependency: " . $e->getMessage());
        }

        /** @var T */
        return $reflectionClass->newInstanceArgs($dependencies);
    }

    /**
     * @param ReflectionParameter[] $reflectionParameters
     * @return mixed[]
     * @throws ContainerException
     * @throws NotFoundException
     * @throws ReflectionException
     */
    private function resolveParameters(array $reflectionParameters): array
    {
        $parameters = [];
        foreach ($reflectionParameters as $reflectionParameter) {
            $parameters[] = $this->resolveParameter($reflectionParameter);
        }

        return $parameters;
    }

    /**
     * @param ReflectionParameter $reflectionParameter
     * @return mixed
     * @throws ContainerException
     * @throws NotFoundException
     * @throws ReflectionException
     */
    private function resolveParameter(ReflectionParameter $reflectionParameter): mixed
    {
        $reflectionParameterType = $reflectionParameter->getType();

        if ($reflectionParameterType instanceof ReflectionUnionType) {
            foreach ($reflectionParameterType->getTypes() as $reflectionNamedType) {
                try {
                    return $this->resolveParameterTypeHint($reflectionParameter, $reflectionNamedType);
                } catch (Exception) {
                }
            }
        } elseif ($reflectionParameterType instanceof ReflectionNamedType) {
            return $this->resolveParameterTypeHint($reflectionParameter, $reflectionParameterType);
        }

        throw new ContainerException("Parameter '" . $reflectionParameter->getName() . "' has no type hint available.");
    }

    /**
     * @param ReflectionParameter $reflectionParameter
     * @param ReflectionNamedType $reflectionParameterType
     *
     * @return mixed
     * @throws ContainerException
     * @throws NotFoundException
     * @throws ReflectionException
     */
    private function resolveParameterTypeHint(
        ReflectionParameter $reflectionParameter,
        ReflectionNamedType $reflectionParameterType
    ): mixed {
        /** @var class-string */
        $id = $reflectionParameterType->getName();
        if ($this->has($id)) {
            return $this->get($id);
        }

        if ($reflectionParameter->isDefaultValueAvailable()) {
            return $reflectionParameter->getDefaultValue();
        }

        if ($reflectionParameter->allowsNull()) {
            return null;
        }

        throw new ContainerException(
            "Unable to instantiate parameter '" . $reflectionParameter->getName() . "'. " .
            "It should has a default value (or nullable) and is not instantiable by the container."
        );
    }
}
