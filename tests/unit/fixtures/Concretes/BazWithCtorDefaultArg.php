<?php

declare(strict_types=1);

namespace Kraber\Test\Unit\Fixtures\Concretes;

use Kraber\Test\Unit\Fixtures\Contracts\BazInterface;

class BazWithCtorDefaultArg implements BazInterface
{
    private string $str = "";
    public function __construct($str = "Hello world !")
    {
        $this->str = $str;
    }
    
    public function returnHelloWorld(): string
    {
        return $this->str;
    }
}
