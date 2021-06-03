<?php

declare(strict_types=1);

namespace Kraber\Container;

use Psr\Container\ContainerInterface;
use Exception;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionUnionType;
use ReflectionNamedType;
use ReflectionException;
use WeakReference;

class Container implements ContainerInterface
{
    /**
     * @var array<class-string<object>, ContainerEntry<object>>
     */
    private array $register = [];

    /**
     * @var array<class-string<object>, WeakReference<object>>
     */
    private array $instances = [];

    /**
     * @template C of object
     * @param class-string<C> $class
     * @param bool $shared
     * @param bool $autoload
     * @return ContainerEntry<C>
     * @throws ContainerException If provided class is not defined.
     */
    public function add(string $class, bool $shared = false, bool $autoload = true): ContainerEntry
    {
        if (!class_exists($class, $autoload)) {
            throw new ContainerException("Class '" . $class . "' is not defined.");
        }

        $this->register[$class] = new ContainerEntry($class, $shared);
        return $this->register[$class];
    }

    /**
     * @template I of object
     * @template C of object
     * @param class-string<I> $interface
     * @param class-string<C> $class
     * @param bool $shared
     * @param bool $autoload
     * @return ContainerEntry<C>
     * @throws ContainerException If provided interface is invalid or class does not implement interface.
     */
    public function bind(string $interface, string $class, bool $shared = false, bool $autoload = true): ContainerEntry
    {
        if (!(interface_exists($interface, $autoload) && is_subclass_of($class, $interface))) {
            throw new ContainerException(
                "Could not bind interface '" . $interface . "' to concrete class '" . $class . "'." .
                "Please make sure interface exists and class implements it."
            );
        }

        $this->register[$interface] = new ContainerEntry($class, $shared);
        return $this->register[$interface];
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @template I of object
     * @template C of object
     *
     * @param class-string<I> $id Identifier of the entry to look for.
     *
     * @return C
     * @throws NotFoundException  No entry was found for **this** identifier.
     * @throws ContainerException Error while retrieving the entry.
     * @throws ReflectionException
     */
    public function get(string $id): object
    {
        if (!$this->has($id)) {
            throw new NotFoundException("Unable to found concrete implementation for '" . $id . "'.");
        }

        $instance = null;
        $containerEntry = $this->register[$id];
        if ($containerEntry->isShared() && isset($this->instances[$id])) {
            $instance = $this->instances[$id]->get();
        }

        if ($instance === null) {
            $instance = $this->resolve($containerEntry->getIdentifier(), $containerEntry->getArguments());
            if ($containerEntry->isShared()) {
                $this->instances[$id] = WeakReference::create($instance);
            }
        }

        /** @var C */
        return $instance;
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @template I of object
     * @param class-string<I> $id Identifier of the entry to look for.
     * @return bool
     */
    public function has(string $id): bool
    {
        return isset($this->register[$id]);
    }

    /**
     * @template C of object
     *
     * @param class-string<C> $concrete
     * @param array<string|class-string, mixed> $arguments
     *
     * @return C
     * @throws ContainerException Error while resolving dependencies.
     * @throws ReflectionException If an error occurred during reflection.
     * @throws NotFoundException
     */
    private function resolve(string $concrete, mixed $arguments = []): object
    {
        $reflectionClass = new ReflectionClass($concrete);
        if (!$reflectionClass->isInstantiable()) {
            throw new ContainerException("Class '" . $concrete . "' is not instantiable.");
        }

        $reflectionClassCtor = $reflectionClass->getConstructor();
        if ($reflectionClassCtor === null) {
            return $reflectionClass->newInstance();
        }

        return $this->resolveWithConstructor($reflectionClass, $reflectionClassCtor, $arguments);
    }

    /**
     * @template C of object
     *
     * @param ReflectionClass<C> $reflectionClass
     * @param ReflectionMethod $reflectionClassCtor
     * @param array<string|class-string, mixed> $arguments
     *
     * @return C
     * @throws ContainerException Error while resolving dependencies.
     * @throws ReflectionException If an error occurred during reflection.
     * @throws NotFoundException
     */
    private function resolveWithConstructor(
        ReflectionClass $reflectionClass,
        ReflectionMethod $reflectionClassCtor,
        array $arguments = []
    ): object {
        try {
            $dependencies = $this->resolveParameters($reflectionClassCtor->getParameters(), $arguments);
        } catch (ContainerException $e) {
            throw new ContainerException(
                "Class '" . $reflectionClass->getName() . "' unable to resolve dependency. " . $e->getMessage()
            );
        }

        return $reflectionClass->newInstanceArgs($dependencies);
    }

    /**
     * @param ReflectionParameter[] $reflectionParameters
     * @param array<string|class-string, mixed> $arguments
     *
     * @return mixed[]
     * @throws ContainerException
     * @throws NotFoundException
     * @throws ReflectionException
     */
    private function resolveParameters(array $reflectionParameters, array $arguments = []): array
    {
        $parameters = [];
        foreach ($reflectionParameters as $reflectionParameter) {
            $parameter = $this->resolveParameter($reflectionParameter, $arguments);
            if ($reflectionParameter->isVariadic()) {
                $parameters = $parameters + [...$parameter];
            } else {
                $parameters[] = $parameter;
            }
        }

        return $parameters;
    }

    /**
     * @param ReflectionParameter $reflectionParameter
     * @param array<string|class-string, mixed> $arguments
     *
     * @return mixed
     * @throws ContainerException
     * @throws NotFoundException
     * @throws ReflectionException
     */
    private function resolveParameter(ReflectionParameter $reflectionParameter, array $arguments = []): mixed
    {
        $argName = '$' . $reflectionParameter->getName();
        if (isset($arguments[$argName])) {
            return $arguments[$argName];
        }

        $reflectionParameterType = $reflectionParameter->getType();
        if ($reflectionParameterType instanceof ReflectionUnionType) {
            return $this->resolveParameterUnionTypeHint($reflectionParameter, $reflectionParameterType, $arguments);
        } elseif ($reflectionParameterType instanceof ReflectionNamedType) {
            return $this->resolveParameterTypeHint($reflectionParameter, $reflectionParameterType, $arguments);
        }

        throw new ContainerException(
            "Unable to resolve parameter '" . $reflectionParameter->getName() . "', not type hint provided."
        );
    }

    /**
     * @param ReflectionParameter $reflectionParameter
     * @param ReflectionUnionType $reflectionParameterUnionType
     * @param array<string|class-string, mixed> $arguments
     *
     * @return mixed
     * @throws ContainerException
     */
    private function resolveParameterUnionTypeHint(
        ReflectionParameter $reflectionParameter,
        ReflectionUnionType $reflectionParameterUnionType,
        array $arguments = []
    ): mixed {
        foreach ($reflectionParameterUnionType->getTypes() as $reflectionNamedType) {
            try {
                return $this->resolveParameterTypeHint($reflectionParameter, $reflectionNamedType, $arguments);
            } catch (Exception) {
            }
        }

        throw new ContainerException("Unable to resolve parameter '" . $reflectionParameter->getName() . "'.");
    }


    /**
     * @param ReflectionParameter $reflectionParameter
     * @param ReflectionNamedType $reflectionParameterType
     * @param array<string|class-string, mixed> $arguments
     *
     * @return mixed
     * @throws ContainerException
     * @throws NotFoundException
     * @throws ReflectionException
     */
    private function resolveParameterTypeHint(
        ReflectionParameter $reflectionParameter,
        ReflectionNamedType $reflectionParameterType,
        array $arguments = []
    ): mixed {
        $id = $reflectionParameterType->getName();
        if (!$reflectionParameterType->isBuiltin() && (class_exists($id, false) || interface_exists($id, false))) {
            $class = $arguments[$id] ?? null;
            if ($class !== null && (class_exists($class, false) || interface_exists($class, false))) {
                if ($this->has($class)) {
                    return $this->get($class);
                } else {
                    throw new ContainerException(
                        "Could to resolve parameter '" . $reflectionParameter->getName() . "' using '" . $class . "'."
                    );
                }
            }

            if ($this->has($id)) {
                return $this->get($id);
            }
        }

        if ($reflectionParameter->isDefaultValueAvailable()) {
            return $reflectionParameter->getDefaultValue();
        }

        if ($reflectionParameter->allowsNull()) {
            return null;
        }

        throw new ContainerException("Could to resolve parameter '" . $reflectionParameter->getName() . "'.");
    }
}
