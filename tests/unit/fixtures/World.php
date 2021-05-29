<?php

declare(strict_types=1);

namespace Kraber\Test\Unit\Fixtures;

class World implements WorldInterface
{
	public function returnWorld() : string {
		return "World !";
	}
}
