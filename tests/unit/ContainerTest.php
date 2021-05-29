<?php

namespace Kraber\Test\Unit;

use Kraber\Test\TestCase;
use Kraber\Container\Container;

class ContainerTest extends TestCase
{
	public function testBindOnConcreteWithNoConstructor() {
		$container = new Container();
		$container->bind(
			\Kraber\Test\Unit\Fixtures\BazInterface::class,
			\Kraber\Test\Unit\Fixtures\BazClassWithoutCtor::class
		);
		
		$concrete = $container->get(\Kraber\Test\Unit\Fixtures\BazInterface::class);
		$this->assertInstanceOf(\Kraber\Test\Unit\Fixtures\BazClassWithoutCtor::class, $concrete);
		$this->assertEquals("Hello world !", $concrete->returnHelloWorld());
	}
	
	public function testBindOnConcreteWithConstructorNoArgs() {
		$container = new Container();
		$container->bind(
			\Kraber\Test\Unit\Fixtures\BazInterface::class,
			\Kraber\Test\Unit\Fixtures\BazClassWithCtorNoArgs::class
		);
		
		$concrete = $container->get(\Kraber\Test\Unit\Fixtures\BazInterface::class);
		$this->assertInstanceOf(\Kraber\Test\Unit\Fixtures\BazClassWithCtorNoArgs::class, $concrete);
		$this->assertEquals("Hello world !", $concrete->returnHelloWorld());
	}
	
	public function testBindOnConcreteWithConstructorNoDefaultArg() {
		$container = new Container();
		$container->bind(
			\Kraber\Test\Unit\Fixtures\BazInterface::class,
			\Kraber\Test\Unit\Fixtures\BazClassWithCtorNoDefaultArg::class
		);
		
		$container->bind(
			\Kraber\Test\Unit\Fixtures\HelloInterface::class,
			\Kraber\Test\Unit\Fixtures\Hello::class
		);
		
		$concrete = $container->get(\Kraber\Test\Unit\Fixtures\BazInterface::class);
		$this->assertInstanceOf(\Kraber\Test\Unit\Fixtures\BazClassWithCtorNoDefaultArg::class, $concrete);
		$this->assertEquals("Hello", $concrete->returnHelloWorld());
	}
}
