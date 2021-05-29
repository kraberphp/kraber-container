<?php

declare(strict_types=1);

namespace Kraber\Test\Unit\Fixtures;

class BazClassWithoutCtor implements BazInterface
{
	public function returnHelloWorld() : string {
		return "Hello world !";
	}
}
