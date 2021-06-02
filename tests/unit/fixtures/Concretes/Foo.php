<?php

declare(strict_types=1);

namespace Kraber\Test\Unit\Fixtures\Concretes;

class Foo
{
    public function __construct(private int $id, private string $name)
    {
    }

    public function returnHelloWorld(): string
    {
        return "#" . $this->id . " " . $this->name;
    }
}
