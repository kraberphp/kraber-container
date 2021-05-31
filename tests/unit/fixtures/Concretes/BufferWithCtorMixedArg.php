<?php

declare(strict_types=1);

namespace Kraber\Test\Unit\Fixtures\Concretes;

use Kraber\Test\Unit\Fixtures\Contracts\BufferInterface;

class BufferWithCtorMixedArg implements BufferInterface
{
    private mixed $buffer = 0;
    public function __construct(mixed $value)
    {
        $this->buffer = $value;
    }
    
    public function get(): mixed
    {
        return $this->buffer;
    }
}
