<?php

declare(strict_types=1);

namespace Kraber\Test\Unit;

use Kraber\Test\TestCase;
use Kraber\Container\Container;
use Kraber\Container\ContainerException;
use Kraber\Container\NotFoundException;

class ContainerTest extends TestCase
{
    public function testGetWithUnknownIdentifierThrowsException()
    {
        $container = new Container();
        $this->expectException(NotFoundException::class);
        $container->get(\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class);
    }

    public function testBindOnClassWithNoConstructor()
    {
        $container = new Container();
        $container->bind(
            \Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class,
            \Kraber\Test\Unit\Fixtures\Concretes\BazWithoutCtor::class
        );

        $concrete = $container->get(\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class);
        $this->assertInstanceOf(\Kraber\Test\Unit\Fixtures\Concretes\BazWithoutCtor::class, $concrete);
        $this->assertEquals("Hello world !", $concrete->returnHelloWorld());
    }

    public function testAddOnClassWithNoConstructor()
    {
        $container = new Container();
        $container->add(\Kraber\Test\Unit\Fixtures\Concretes\BazWithoutCtor::class);

        $concrete = $container->get(\Kraber\Test\Unit\Fixtures\Concretes\BazWithoutCtor::class);
        $this->assertInstanceOf(\Kraber\Test\Unit\Fixtures\Concretes\BazWithoutCtor::class, $concrete);
        $this->assertEquals("Hello world !", $concrete->returnHelloWorld());
    }

    public function testBindOnClassWithNoConstructorAndSharedEnabledReturnsSameInstance()
    {
        $container = new Container();
        $container->bind(
            \Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class,
            \Kraber\Test\Unit\Fixtures\Concretes\BazWithoutCtor::class,
            true
        );

        $concrete1 = $container->get(\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class);
        $concrete2 = $container->get(\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class);
        $this->assertInstanceOf(\Kraber\Test\Unit\Fixtures\Concretes\BazWithoutCtor::class, $concrete1);
        $this->assertInstanceOf(\Kraber\Test\Unit\Fixtures\Concretes\BazWithoutCtor::class, $concrete2);
        $this->assertEquals("Hello world !", $concrete1->returnHelloWorld());
        $this->assertEquals("Hello world !", $concrete2->returnHelloWorld());
        $this->assertSame($concrete2, $concrete1);
    }

    public function testAddOnClassWithNoConstructorAndSharedEnabledReturnsSameInstance()
    {
        $container = new Container();
        $container->add(\Kraber\Test\Unit\Fixtures\Concretes\BazWithoutCtor::class, true);

        $concrete1 = $container->get(\Kraber\Test\Unit\Fixtures\Concretes\BazWithoutCtor::class);
        $concrete2 = $container->get(\Kraber\Test\Unit\Fixtures\Concretes\BazWithoutCtor::class);
        $this->assertInstanceOf(\Kraber\Test\Unit\Fixtures\Concretes\BazWithoutCtor::class, $concrete1);
        $this->assertInstanceOf(\Kraber\Test\Unit\Fixtures\Concretes\BazWithoutCtor::class, $concrete2);
        $this->assertEquals("Hello world !", $concrete1->returnHelloWorld());
        $this->assertEquals("Hello world !", $concrete2->returnHelloWorld());
        $this->assertSame($concrete2, $concrete1);
    }

    public function testSetSharedReturnsSameInstance()
    {
        $container = new Container();
        $container->add(\Kraber\Test\Unit\Fixtures\Concretes\BazWithoutCtor::class)->setShared();

        $concrete1 = $container->get(\Kraber\Test\Unit\Fixtures\Concretes\BazWithoutCtor::class);
        $concrete2 = $container->get(\Kraber\Test\Unit\Fixtures\Concretes\BazWithoutCtor::class);
        $this->assertInstanceOf(\Kraber\Test\Unit\Fixtures\Concretes\BazWithoutCtor::class, $concrete1);
        $this->assertInstanceOf(\Kraber\Test\Unit\Fixtures\Concretes\BazWithoutCtor::class, $concrete2);
        $this->assertEquals("Hello world !", $concrete1->returnHelloWorld());
        $this->assertEquals("Hello world !", $concrete2->returnHelloWorld());
        $this->assertSame($concrete2, $concrete1);
    }

    public function testBindOnClassWithCtorNoArgs()
    {
        $container = new Container();
        $container->bind(
            \Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class,
            \Kraber\Test\Unit\Fixtures\Concretes\BazWithCtorNoArgs::class
        );

        $concrete = $container->get(\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class);
        $this->assertInstanceOf(\Kraber\Test\Unit\Fixtures\Concretes\BazWithCtorNoArgs::class, $concrete);
        $this->assertEquals("Hello world !", $concrete->returnHelloWorld());
    }

    public function testAddOnClassWithCtorNoArgs()
    {
        $container = new Container();
        $container->add(\Kraber\Test\Unit\Fixtures\Concretes\BazWithCtorNoArgs::class);

        $concrete = $container->get(\Kraber\Test\Unit\Fixtures\Concretes\BazWithCtorNoArgs::class);
        $this->assertInstanceOf(\Kraber\Test\Unit\Fixtures\Concretes\BazWithCtorNoArgs::class, $concrete);
        $this->assertEquals("Hello world !", $concrete->returnHelloWorld());
    }

    public function testBindOnClassWithCtorNoDefaultArg()
    {
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

    public function testBindOnClassWithCtorAndOneDefaultArg()
    {
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

    public function testBindOnClassWithCtorAndTwoConcreteArgs()
    {
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

    public function testBindOnClassWithCtorArgNoDefaultValueAllowNull()
    {
        $container = new Container();
        $container->bind(
            \Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class,
            \Kraber\Test\Unit\Fixtures\Concretes\BazWithCtorNoDefaultArgAllowNull::class
        );

        $concrete = $container->get(\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class);
        $this->assertInstanceOf(
            \Kraber\Test\Unit\Fixtures\Concretes\BazWithCtorNoDefaultArgAllowNull::class,
            $concrete
        );
        $this->assertNull($this->getPropertyValue($concrete, 'str'));
    }

    public function testBindOnClassWithCtorUsingUnionTypeHint()
    {
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

    public function testAddArgumentAndBindWithCtorUsingUnionTypeHint()
    {
        $container = new Container();
        $container->bind(
            \Kraber\Test\Unit\Fixtures\Contracts\HelloInterface::class,
            \Kraber\Test\Unit\Fixtures\Concretes\Hello::class
        );

        $container->add(\Kraber\Test\Unit\Fixtures\Concretes\BazWithCtorUnionTypeHint::class)
            ->addArgument(
                \Kraber\Test\Unit\Fixtures\Contracts\HelloInterface::class,
                \Kraber\Test\Unit\Fixtures\Contracts\HelloInterface::class
            );

        $concrete = $container->get(\Kraber\Test\Unit\Fixtures\Concretes\BazWithCtorUnionTypeHint::class);
        $this->assertInstanceOf(\Kraber\Test\Unit\Fixtures\Concretes\BazWithCtorUnionTypeHint::class, $concrete);
        $this->assertEquals("Hello", $concrete->returnHelloWorld());
    }

    public function testBindOnClassWithCtorUsingUnionTypeHintThrowsExceptionIfUnresolvable()
    {
        $container = new Container();
        $container->bind(
            \Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class,
            \Kraber\Test\Unit\Fixtures\Concretes\BazWithCtorUnionTypeHint::class
        );

        $this->expectException(ContainerException::class);
        $container->get(\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class);
    }

    public function testAddClassWithCtorUsingUnionTypeHintThrowsExceptionIfUnresolvable()
    {
        $container = new Container();
        $container->add(\Kraber\Test\Unit\Fixtures\Concretes\BazWithCtorUnionTypeHint::class)
            ->addArgument(
                \Kraber\Test\Unit\Fixtures\Contracts\HelloInterface::class,
                \Kraber\Test\Unit\Fixtures\Concretes\Hello::class
            );

        $this->expectException(ContainerException::class);
        $container->get(\Kraber\Test\Unit\Fixtures\Concretes\BazWithCtorUnionTypeHint::class);
    }

    public function testBindOnClassWithCtorUsingUnionTypeHintInOrder()
    {
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

    public function testBindOnClassWithCtorUsingUnionTypeHintAndMissingFirstTypeBinding()
    {
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

    public function testBindOnClassWithCtorUsingUnionTypeHintAllowingNullAndMissingTypeBindingResolveWithNull()
    {
        $container = new Container();
        $container->bind(
            \Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class,
            \Kraber\Test\Unit\Fixtures\Concretes\BazWithCtorUnionTypeHintAllowingNull::class
        );

        $concrete = $container->get(\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class);
        $this->assertInstanceOf(
            \Kraber\Test\Unit\Fixtures\Concretes\BazWithCtorUnionTypeHintAllowingNull::class,
            $concrete
        );
        $this->assertNull($this->getPropertyValue($concrete, 'concrete'));
        $this->assertEquals("", $concrete->returnHelloWorld());
    }

    public function testBindOnClassWithCtorUsingMixedTypeHintResolveWithNull()
    {
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

    public function testBindOnClassNotImplementingInterfaceThrowsException()
    {
        $container = new Container();

        $this->expectException(ContainerException::class);
        $container->bind(
            \Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class,
            \Kraber\Test\Unit\Fixtures\Concretes\Hello::class
        );
    }

    public function testBindOnClassWithPrivateConstructorThrowsException()
    {
        $container = new Container();
        $container->bind(
            \Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class,
            \Kraber\Test\Unit\Fixtures\Concretes\BazWithPrivateCtor::class
        );

        $this->expectException(ContainerException::class);
        $container->get(\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class);
    }

    public function testBindOnAbstractClassThrowsException()
    {
        $container = new Container();
        $container->bind(
            \Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class,
            \Kraber\Test\Unit\Fixtures\Concretes\AbstractBazClass::class
        );

        $this->expectException(ContainerException::class);
        $container->get(\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class);
    }

    public function testBindOnClassWithCtorAndUnresolvableDependencyThrowsException()
    {
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

    public function testBindOnClassWithCtorAndNoTypeHintAndNoDefaultValueThrowsException()
    {
        $container = new Container();
        $container->bind(
            \Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class,
            \Kraber\Test\Unit\Fixtures\Concretes\BazWithCtorNoTypeHintNoDefaultValue::class
        );

        $this->expectException(ContainerException::class);
        $container->get(\Kraber\Test\Unit\Fixtures\Contracts\BazInterface::class);
    }

    public function testAddOnClassWithAddArgument()
    {
        $container = new Container();
        $container->add(\Kraber\Test\Unit\Fixtures\Concretes\Point::class)
            ->addArgument('$x', 42.5)
            ->addArgument('$y', 32.68)
            ->addArgument('$z', 128.88);

        $concrete = $container->get(\Kraber\Test\Unit\Fixtures\Concretes\Point::class);
        $this->assertInstanceOf(\Kraber\Test\Unit\Fixtures\Concretes\Point::class, $concrete);
        $this->assertSame(42.5, $this->getPropertyValue($concrete, 'x'));
        $this->assertSame(32.68, $this->getPropertyValue($concrete, 'y'));
        $this->assertSame(128.88, $this->getPropertyValue($concrete, 'z'));
    }

    public function testAddOnClassWithAddArguments()
    {
        $container = new Container();
        $container->add(\Kraber\Test\Unit\Fixtures\Concretes\Point::class)
            ->addArguments([
                '$x' => 42.5,
                '$y' => 32.68,
                '$z' => 128.88
            ]);

        $concrete = $container->get(\Kraber\Test\Unit\Fixtures\Concretes\Point::class);
        $this->assertInstanceOf(\Kraber\Test\Unit\Fixtures\Concretes\Point::class, $concrete);
        $this->assertSame(42.5, $this->getPropertyValue($concrete, 'x'));
        $this->assertSame(32.68, $this->getPropertyValue($concrete, 'y'));
        $this->assertSame(128.88, $this->getPropertyValue($concrete, 'z'));
    }

    public function testAddOnClassWithMultipleAddArgumentsCalls()
    {
        $container = new Container();
        $container->add(\Kraber\Test\Unit\Fixtures\Concretes\Point::class)
            ->addArguments([
                '$x' => 42.5,
                '$y' => 32.68
            ])
            ->addArguments(['$z' => 128.88]);

        $concrete = $container->get(\Kraber\Test\Unit\Fixtures\Concretes\Point::class);
        $this->assertInstanceOf(\Kraber\Test\Unit\Fixtures\Concretes\Point::class, $concrete);
        $this->assertSame(42.5, $this->getPropertyValue($concrete, 'x'));
        $this->assertSame(32.68, $this->getPropertyValue($concrete, 'y'));
        $this->assertSame(128.88, $this->getPropertyValue($concrete, 'z'));
    }

    public function testAddOnClassWithMultipleAddArgumentsAndOneAddArgumentCall()
    {
        $container = new Container();
        $container->add(\Kraber\Test\Unit\Fixtures\Concretes\Point::class)
            ->addArguments([
                '$x' => 42.5,
                '$y' => 32.68
            ])
            ->addArgument('$z', 128.88);

        $concrete = $container->get(\Kraber\Test\Unit\Fixtures\Concretes\Point::class);
        $this->assertInstanceOf(\Kraber\Test\Unit\Fixtures\Concretes\Point::class, $concrete);
        $this->assertSame(42.5, $this->getPropertyValue($concrete, 'x'));
        $this->assertSame(32.68, $this->getPropertyValue($concrete, 'y'));
        $this->assertSame(128.88, $this->getPropertyValue($concrete, 'z'));
    }

    public function testAddArgumentWithVariadicClassCtor()
    {
        $args = [
            new \Kraber\Test\Unit\Fixtures\Concretes\Point(2, 8, 64),
            new \Kraber\Test\Unit\Fixtures\Concretes\Point(4, 16, 128),
            new \Kraber\Test\Unit\Fixtures\Concretes\Point(8, 32, 256),
        ];

        $container = new Container();
        $container->add(\Kraber\Test\Unit\Fixtures\Concretes\Path::class)
            ->addArguments(['$points' => $args]);

        $concrete = $container->get(\Kraber\Test\Unit\Fixtures\Concretes\Path::class);
        $this->assertInstanceOf(\Kraber\Test\Unit\Fixtures\Concretes\Path::class, $concrete);
        $this->assertEquals($args, $this->getPropertyValue($concrete, 'points'));
    }

    public function testAddUndefinedClassThrowsException()
    {
        $container = new Container();
        $this->expectException(ContainerException::class);
        $container->add(\Kraber\Test\Unit\Fixtures\Concretes\UndefinedPoint::class);
    }

    public function testAddArgumentsWithInvalidKeyThrowsException()
    {
        $container = new Container();
        $this->expectException(ContainerException::class);
        $container->add(\Kraber\Test\Unit\Fixtures\Concretes\Point::class)
            ->addArguments([
                42 => 42.5,
                '$y' => 32.68,
                '$z', 128.88
            ]);
    }
}
