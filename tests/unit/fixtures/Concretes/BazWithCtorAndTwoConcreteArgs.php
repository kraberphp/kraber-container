<?php

declare(strict_types=1);

namespace Kraber\Test\Unit\Fixtures\Concretes;

use Kraber\Test\Unit\Fixtures\Contracts\BazInterface;
use Kraber\Test\Unit\Fixtures\Contracts\HelloInterface;
use Kraber\Test\Unit\Fixtures\Contracts\WorldInterface;

class BazWithCtorAndTwoConcreteArgs implements BazInterface
{
    private HelloInterface $hello;
    private WorldInterface $world;
    public function __construct(HelloInterface $hello, WorldInterface $world)
    {
        $this->hello = $hello;
        $this->world = $world;
    }
    
    public function returnHelloWorld(): string
    {
        return $this->hello->returnHello() . " " . $this->world->returnWorld();
    }
}
