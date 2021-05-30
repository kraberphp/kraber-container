<?php

declare(strict_types=1);

namespace Kraber\Container;

use Psr\Container\ContainerInterface;
use ReflectionClass,
	ReflectionParameter,
	ReflectionUnionType,
	ReflectionNamedType,
	ReflectionException,
	Exception;

class Container implements ContainerInterface
{
	private array $instances = [];
	
	public function bind(string $id, string $concrete) {
		$this->instances[$id] = $concrete;
	}
	
	/**
	 * Finds an entry of the container by its identifier and returns it.
	 *
	 * @param string $id Identifier of the entry to look for.
	 * @return mixed Entry.
	 * @throws NotFoundException  No entry was found for **this** identifier.
	 * @throws ContainerException Error while retrieving the entry.
	 */
	public function get(string $id) : mixed {
		if (!$this->has($id)) {
			throw new NotFoundException("Unable to found concrete implementation for '".$id."'.");
		}
		
		return $this->resolve($this->instances[$id]);
	}
	
	/**
	 * Returns true if the container can return an entry for the given identifier.
	 * Returns false otherwise.
	 *
	 * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
	 * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
	 *
	 * @param string $id Identifier of the entry to look for.
	 * @return bool
	 */
	public function has(string $id): bool {
		return isset($this->instances[$id]);
	}
	
	/**
	 * @param string $concrete
	 * @return mixed
	 * @throws ContainerException Error while resolving dependencies.
	 * @throws ReflectionException If an error occurred during reflection.
	 */
	private function resolve(string $concrete) : mixed {
		$reflectionClass = new ReflectionClass($concrete);
		if (!$reflectionClass->isInstantiable()) {
			throw new ContainerException("Class '".$concrete."' is not an instantiable.");
		}
		
		$concreteCtor = $reflectionClass->getConstructor();
		if ($concreteCtor === null) {
			return $reflectionClass->newInstance();
		}
		
		try {
			$dependencies = $this->resolveParameters($concreteCtor->getParameters());
		}
		catch (ContainerException $e) {
			throw new ContainerException("Class '".$concrete."' unable to resolve dependency: ".$e->getMessage());
		}
		
		return $reflectionClass->newInstanceArgs($dependencies);
	}
	
	/**
	 * @param ReflectionParameter[] $reflectionParameters
	 * @return array
	 * @throws ContainerException
	 * @throws NotFoundException
	 * @throws ReflectionException
	 */
	private function resolveParameters(array $reflectionParameters) : array {
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
	private function resolveParameter(ReflectionParameter $reflectionParameter) : mixed {
		$reflectionParameterType = $reflectionParameter->getType();
		
		if ($reflectionParameterType instanceof ReflectionUnionType) {
			foreach ($reflectionParameterType->getTypes() as $reflectionNamedType) {
				try {
					return $this->resolveParameterTypeHint($reflectionParameter, $reflectionNamedType);
				}
				catch (Exception) {
				}
			}
		}
		elseif ($reflectionParameterType instanceof ReflectionNamedType) {
			return $this->resolveParameterTypeHint($reflectionParameter, $reflectionParameterType);
		}
		
		throw new ContainerException("Parameter '".$reflectionParameter->getName()."' has no type hint available.");
	}
	
	private function resolveParameterTypeHint(ReflectionParameter $reflectionParameter, ReflectionNamedType $reflectionParameterType) : mixed {
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
			"Parameter '".$reflectionParameter->getName()."' has no default value and is not instantiable by the container."
		);
	}
}
