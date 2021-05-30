<?php

namespace Kraber\Test\Unit;

use Kraber\Test\TestCase;
use Kraber\Container\{
	Container,
	ContainerException,
	NotFoundException
};

class ContainerTest extends TestCase
{
	public function testGetWithUnknownIdentifierThrowsException() {
		$container = new Container();
		$this->expectException(NotFoundException::class);
		$container->get(\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class);
	}
	
	public function testBindOnConcreteWithNoConstructor() {
		$container = new Container();
		$container->bind(
			\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class,
			\Kraber\Test\Unit\Fixtures\Concretes\BazWithoutCtor::class
		);
		
		$concrete = $container->get(\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class);
		$this->assertInstanceOf(\Kraber\Test\Unit\Fixtures\Concretes\BazWithoutCtor::class, $concrete);
		$this->assertEquals("Hello world !", $concrete->returnHelloWorld());
	}
	
	public function testBindOnConcreteWithConstructorNoArgs() {
		$container = new Container();
		$container->bind(
			\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class,
			\Kraber\Test\Unit\Fixtures\Concretes\BazWithCtorNoArgs::class
		);
		
		$concrete = $container->get(\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class);
		$this->assertInstanceOf(\Kraber\Test\Unit\Fixtures\Concretes\BazWithCtorNoArgs::class, $concrete);
		$this->assertEquals("Hello world !", $concrete->returnHelloWorld());
	}
	
	public function testBindOnConcreteWithConstructorNoDefaultArg() {
		$container = new Container();
		$container->bind(
			\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class,
			\Kraber\Test\Unit\Fixtures\Concretes\BazWithCtorNoDefaultArg::class
		);
		
		$container->bind(
			\Kraber\Test\Unit\Fixtures\Contracts\HelloInterface::class,
			\Kraber\Test\Unit\Fixtures\Concretes\Hello::class
		);
		
		$concrete = $container->get(\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class);
		$this->assertInstanceOf(\Kraber\Test\Unit\Fixtures\Concretes\BazWithCtorNoDefaultArg::class, $concrete);
		$this->assertEquals("Hello", $concrete->returnHelloWorld());
	}
	
	public function testBindOnConcreteWithConstructorAndOneDefaultArg() {
		$container = new Container();
		$container->bind(
			\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class,
			\Kraber\Test\Unit\Fixtures\Concretes\BazWithCtorAndOneDefaultArg::class
		);
		
		$container->bind(
			\Kraber\Test\Unit\Fixtures\Contracts\HelloInterface::class,
			\Kraber\Test\Unit\Fixtures\Concretes\Hello::class
		);
		
		$concrete = $container->get(\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class);
		$this->assertInstanceOf(\Kraber\Test\Unit\Fixtures\Concretes\BazWithCtorAndOneDefaultArg::class, $concrete);
		$this->assertEquals("Hello world !", $concrete->returnHelloWorld());
	}
	
	public function testBindOnConcreteWithConstructorAndTwoConcreteArgs() {
		$container = new Container();
		$container->bind(
			\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class,
			\Kraber\Test\Unit\Fixtures\Concretes\BazWithCtorAndTwoConcreteArgs::class
		);
		
		$container->bind(
			\Kraber\Test\Unit\Fixtures\Contracts\HelloInterface::class,
			\Kraber\Test\Unit\Fixtures\Concretes\Hello::class
		);
		
		$container->bind(
			\Kraber\Test\Unit\Fixtures\Contracts\WorldInterface::class,
			\Kraber\Test\Unit\Fixtures\Concretes\World::class
		);
		
		$concrete = $container->get(\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class);
		$this->assertInstanceOf(\Kraber\Test\Unit\Fixtures\Concretes\BazWithCtorAndTwoConcreteArgs::class, $concrete);
		$this->assertEquals("Hello world !", $concrete->returnHelloWorld());
	}
	
	public function testBindOnConcreteWithConstructorArgNoDefaultValueAllowNull() {
		$container = new Container();
		$container->bind(
			\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class,
			\Kraber\Test\Unit\Fixtures\Concretes\BazWithCtorNoDefaultArgAllowNull::class
		);
		
		$concrete = $container->get(\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class);
		$this->assertInstanceOf(\Kraber\Test\Unit\Fixtures\Concretes\BazWithCtorNoDefaultArgAllowNull::class, $concrete);
		$this->assertNull($this->getPropertyValue($concrete, 'str'));
	}
	
	public function testBindOnConcreteWithConstructorUsingUnionTypeHint() {
		$container = new Container();
		$container->bind(
			\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class,
			\Kraber\Test\Unit\Fixtures\Concretes\BazWithCtorUnionTypeHint::class
		);
		
		$container->bind(
			\Kraber\Test\Unit\Fixtures\Contracts\HelloInterface::class,
			\Kraber\Test\Unit\Fixtures\Concretes\Hello::class
		);
		
		$concrete = $container->get(\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class);
		$this->assertInstanceOf(\Kraber\Test\Unit\Fixtures\Concretes\BazWithCtorUnionTypeHint::class, $concrete);
		$this->assertEquals("Hello", $concrete->returnHelloWorld());
	}
	
	public function testBindOnConcreteWithConstructorUsingUnionTypeHintInOrder() {
		$container = new Container();
		$container->bind(
			\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class,
			\Kraber\Test\Unit\Fixtures\Concretes\BazWithCtorUnionTypeHint::class
		);
		
		$container->bind(
			\Kraber\Test\Unit\Fixtures\Contracts\HelloInterface::class,
			\Kraber\Test\Unit\Fixtures\Concretes\Hello::class
		);
		
		$container->bind(
			\Kraber\Test\Unit\Fixtures\Contracts\WorldInterface::class,
			\Kraber\Test\Unit\Fixtures\Concretes\World::class
		);
		
		$concrete = $container->get(\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class);
		$this->assertInstanceOf(\Kraber\Test\Unit\Fixtures\Concretes\BazWithCtorUnionTypeHint::class, $concrete);
		$this->assertEquals("Hello", $concrete->returnHelloWorld());
	}
	
	public function testBindOnConcreteWithConstructorUsingUnionTypeHintAndMissingFirstTypeBinding() {
		$container = new Container();
		$container->bind(
			\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class,
			\Kraber\Test\Unit\Fixtures\Concretes\BazWithCtorUnionTypeHint::class
		);
		
		$container->bind(
			\Kraber\Test\Unit\Fixtures\Contracts\WorldInterface::class,
			\Kraber\Test\Unit\Fixtures\Concretes\World::class
		);
		
		$concrete = $container->get(\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class);
		$this->assertInstanceOf(\Kraber\Test\Unit\Fixtures\Concretes\BazWithCtorUnionTypeHint::class, $concrete);
		$this->assertEquals("world !", $concrete->returnHelloWorld());
	}
	
	public function testBindOnConcreteWithConstructorUsingUnionTypeHintAllowingNullAndMissingTypeBindingResolveWithNull() {
		$container = new Container();
		$container->bind(
			\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class,
			\Kraber\Test\Unit\Fixtures\Concretes\BazWithCtorUnionTypeHintAllowingNull::class
		);
		
		$concrete = $container->get(\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class);
		$this->assertInstanceOf(\Kraber\Test\Unit\Fixtures\Concretes\BazWithCtorUnionTypeHintAllowingNull::class, $concrete);
		$this->assertNull($this->getPropertyValue($concrete, 'concrete'));
		$this->assertEquals("", $concrete->returnHelloWorld());
	}
	
	public function testBindOnConcreteWithConstructorUsingMixedTypeHintResolveWithNull() {
		$container = new Container();
		$container->bind(
			\Kraber\Test\Unit\Fixtures\Contracts\BufferInterface::class,
			\Kraber\Test\Unit\Fixtures\Concretes\BufferWithCtorMixedArg::class
		);
		
		$concrete = $container->get(\Kraber\Test\Unit\Fixtures\Contracts\BufferInterface::class);
		$this->assertInstanceOf(\Kraber\Test\Unit\Fixtures\Concretes\BufferWithCtorMixedArg::class, $concrete);
		$this->assertNull($this->getPropertyValue($concrete, 'buffer'));
		$this->assertNull($concrete->get());
	}
	
	public function testBindOnConcreteWithConstructorNoTypeHintAndADocBlockWithParamTypeHint() {
		$container = new Container();
		$container->bind(
			\Kraber\Test\Unit\Fixtures\Contracts\BufferInterface::class,
			\Kraber\Test\Unit\Fixtures\Concretes\BufferWithCtorNoTypeHintAndADocBlock::class
		);
		
		$concrete = $container->get(\Kraber\Test\Unit\Fixtures\Contracts\BufferInterface::class);
		$this->assertInstanceOf(\Kraber\Test\Unit\Fixtures\Concretes\BufferWithCtorNoTypeHintAndADocBlock::class, $concrete);
		$this->assertNull($this->getPropertyValue($concrete, 'buffer'));
		$this->assertNull($concrete->get());
	}
	
	public function testBindOnConcreteWithPrivateConstructorThrowsException() {
		$container = new Container();
		$container->bind(
			\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class,
			\Kraber\Test\Unit\Fixtures\Concretes\BazWithPrivateCtor::class
		);
		
		$this->expectException(ContainerException::class);
		$container->get(\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class);
	}
	
	public function testBindOnAbstractClassThrowsException() {
		$container = new Container();
		$container->bind(
			\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class,
			\Kraber\Test\Unit\Fixtures\Concretes\BazAbstractClass::class
		);
		
		$this->expectException(ContainerException::class);
		$container->get(\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class);
	}
	
	public function testBindOnConcreteWithConstructorAndUnresolvableDependencyThrowsException() {
		$container = new Container();
		$container->bind(
			\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class,
			\Kraber\Test\Unit\Fixtures\Concretes\BazWithCtorAndTwoConcreteArgs::class
		);
		
		$container->bind(
			\Kraber\Test\Unit\Fixtures\Contracts\HelloInterface::class,
			\Kraber\Test\Unit\Fixtures\Concretes\Hello::class
		);
		
		$this->expectException(ContainerException::class);
		$container->get(\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class);
	}
	
	public function testBindOnConcreteWithConstructorAndNoTypeHintAndNoDefaultValueThrowsException() {
		$container = new Container();
		$container->bind(
			\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class,
			\Kraber\Test\Unit\Fixtures\Concretes\BazWithCtorNoTypeHintNoDefaultValue::class
		);
		
		$this->expectException(ContainerException::class);
		$container->get(\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class);
	}
}
