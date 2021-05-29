<?php

declare(strict_types=1);

namespace Kraber\Test\Unit\Fixtures;

class Hello implements HelloInterface
{
	public function returnHello() : string {
		return "Hello";
	}
}
