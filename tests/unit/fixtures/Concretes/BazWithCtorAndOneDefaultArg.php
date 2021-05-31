<?php

declare(strict_types=1);

namespace Kraber\Test\Unit\Fixtures\Concretes;

use Kraber\Test\Unit\Fixtures\Contracts\BazInterface;
use Kraber\Test\Unit\Fixtures\Contracts\HelloInterface;

class BazWithCtorAndOneDefaultArg implements BazInterface
{
    private HelloInterface $hello;
    private string $world;
    public function __construct(HelloInterface $hello, string $world = "world !")
    {
        $this->hello = $hello;
        $this->world = $world;
    }
    
    public function returnHelloWorld(): string
    {
        return $this->hello->returnHello() . " " . $this->world;
    }
}
