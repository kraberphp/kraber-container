<?php

declare(strict_types=1);

namespace Kraber\Test\Unit\Fixtures\Concretes;

use Kraber\Test\Unit\Fixtures\Contracts\HelloInterface;

class Hello implements HelloInterface
{
    public function returnHello(): string
    {
        return "Hello";
    }
}
