<?php

declare(strict_types=1);

namespace Kraber\Container;

use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use ReflectionType;

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
		
		$concrete = $this->instances[$id];
		return $this->resolve($concrete);
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
	 * @param $concrete
	 * @return mixed
	 * @throws ContainerException Error while resolving dependencies.
	 * @throws ReflectionException If an error occurred during reflection.
	 */
	private function resolve($concrete) : mixed {
		$reflectionClass = new ReflectionClass($concrete);
		if (!$reflectionClass->isInstantiable()) {
			throw new ContainerException("Class '".$concrete."' is not instantiable.");
		}
		
		$ctor = $reflectionClass->getConstructor();
		if ($ctor === null) {
			return $reflectionClass->newInstance();
		}
		
		$parameters = $ctor->getParameters();
		try {
			$dependencies = $this->resolveDependencies($parameters);
		}
		catch (ContainerException $e) {
			throw new ContainerException("Unable to resolve '".$concrete."' dependencies: ".$e->getMessage());
		}
		
		return $reflectionClass->newInstanceArgs($dependencies);
	}
	
	/**
	 * @param ReflectionParameter[] $reflectionParameters
	 * @return array
	 */
	private function resolveDependencies(array $reflectionParameters) : array {
		$dependencies = [];
		foreach ($reflectionParameters as $reflectionParameter) {
			$dependencies[] = $this->resolveParameter($reflectionParameter);
		}
		
		return $dependencies;
	}
	
	/**
	 * @param ReflectionParameter $reflectionParameter
	 * @return mixed
	 * @throws ContainerException
	 * @throws NotFoundException
	 * @throws ReflectionException
	 */
	private function resolveParameter(ReflectionParameter $reflectionParameter) : mixed {
		$type = $reflectionParameter->getType()?->getName();
		
		if ($type === null && (!$reflectionParameter->isOptional() || !$reflectionParameter->isDefaultValueAvailable())) {
			return $reflectionParameter->getDefaultValue();
		}
		
		if ($this->has($type)) {
			return $this->get($type);
		}
		
		throw new ContainerException(
			"Parameter '".$reflectionParameter->getName()."' has no default value and is not instantiable by the container."
		);
	}
}
