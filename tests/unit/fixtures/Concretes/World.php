<?php

declare(strict_types=1);

namespace Kraber\Test\Unit\Fixtures\Concretes;

use Kraber\Test\Unit\Fixtures\Contracts\WorldInterface;

class World implements WorldInterface
{
    public function returnWorld(): string
    {
        return "world !";
    }
}
