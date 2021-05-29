<?php

declare(strict_types=1);

namespace Kraber\Test\Unit\Fixtures\Concretes;

use Kraber\Test\Unit\Fixtures\Contracts\BazInterface;

class BazClassWithoutCtor implements BazInterface
{
	public function returnHelloWorld() : string {
		return "Hello world !";
	}
}
