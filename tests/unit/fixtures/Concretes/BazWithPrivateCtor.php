<?php

declare(strict_types=1);

namespace Kraber\Test\Unit\Fixtures\Concretes;

use Kraber\Test\Unit\Fixtures\Contracts\BazInterface;

class BazWithPrivateCtor implements BazInterface
{
	private function __construct() {
	}
	
	public function returnHelloWorld() : string {
		return "Hello world !";
	}
}
