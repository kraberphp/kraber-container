<?php

namespace Kraber\Test\Unit;

use Kraber\Test\TestCase;
use Kraber\Container\Container;

class ContainerTest extends TestCase
{
	public function testBindOnConcreteWithNoConstructor() {
		$container = new Container();
		$container->bind(
			\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class,
			\Kraber\Test\Unit\Fixtures\Concretes\BazClassWithoutCtor::class
		);
		
		$concrete = $container->get(\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class);
		$this->assertInstanceOf(\Kraber\Test\Unit\Fixtures\Concretes\BazClassWithoutCtor::class, $concrete);
		$this->assertEquals("Hello world !", $concrete->returnHelloWorld());
	}
	
	public function testBindOnConcreteWithConstructorNoArgs() {
		$container = new Container();
		$container->bind(
			\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class,
			\Kraber\Test\Unit\Fixtures\Concretes\BazClassWithCtorNoArgs::class
		);
		
		$concrete = $container->get(\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class);
		$this->assertInstanceOf(\Kraber\Test\Unit\Fixtures\Concretes\BazClassWithCtorNoArgs::class, $concrete);
		$this->assertEquals("Hello world !", $concrete->returnHelloWorld());
	}
	
	public function testBindOnConcreteWithConstructorNoDefaultArg() {
		$container = new Container();
		$container->bind(
			\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class,
			\Kraber\Test\Unit\Fixtures\Concretes\BazClassWithCtorNoDefaultArg::class
		);
		
		$container->bind(
			\Kraber\Test\Unit\Fixtures\Contracts\HelloInterface::class,
			\Kraber\Test\Unit\Fixtures\Concretes\Hello::class
		);
		
		$concrete = $container->get(\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class);
		$this->assertInstanceOf(\Kraber\Test\Unit\Fixtures\Concretes\BazClassWithCtorNoDefaultArg::class, $concrete);
		$this->assertEquals("Hello", $concrete->returnHelloWorld());
	}
}
