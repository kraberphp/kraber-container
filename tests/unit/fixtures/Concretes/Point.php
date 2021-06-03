<?php

declare(strict_types=1);

namespace Kraber\Test\Unit\Fixtures\Concretes;

class Point
{
    private float $x = .0;
    private float $y = .0;
    private float $z = .0;

    public function __construct(float $x, float $y, float $z)
    {
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
    }
}
