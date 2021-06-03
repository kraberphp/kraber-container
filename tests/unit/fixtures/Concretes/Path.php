<?php

declare(strict_types=1);

namespace Kraber\Test\Unit\Fixtures\Concretes;

class Path
{
    private array $points = [];
    public function __construct(Point ...$points)
    {
        $this->points = $points;
    }
}
