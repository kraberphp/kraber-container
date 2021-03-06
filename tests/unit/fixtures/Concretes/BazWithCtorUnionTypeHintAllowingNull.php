<?php

declare(strict_types=1);

namespace Kraber\Test\Unit\Fixtures\Concretes;

use Kraber\Test\Unit\Fixtures\Contracts\BazInterface;
use Kraber\Test\Unit\Fixtures\Contracts\HelloInterface;
use Kraber\Test\Unit\Fixtures\Contracts\WorldInterface;

class BazWithCtorUnionTypeHintAllowingNull implements BazInterface
{
    private HelloInterface|WorldInterface|null $concrete;
    public function __construct(HelloInterface|WorldInterface|null $concrete)
    {
        $this->concrete = $concrete;
    }
    
    public function returnHelloWorld(): string
    {
        if ($this->concrete instanceof HelloInterface) {
            return $this->concrete->returnHello();
        } elseif ($this->concrete instanceof WorldInterface) {
            return $this->concrete->returnWorld();
        }
        
        return "";
    }
}
