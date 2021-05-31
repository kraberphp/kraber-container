<?php

declare(strict_types=1);

namespace Kraber\Test\Unit\Fixtures\Concretes;

use Kraber\Test\Unit\Fixtures\Contracts\BazInterface;

abstract class AbstractBazClass implements BazInterface
{
    public function __construct()
    {
    }

    public function returnHelloWorld(): string
    {
        return "Hello world !";
    }
}
