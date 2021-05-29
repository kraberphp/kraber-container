<?php

declare(strict_types=1);

namespace Kraber\Test\Unit\Fixtures\Concretes;

use Kraber\Test\Unit\Fixtures\Contracts\{
	BazInterface,
	HelloInterface
};

class BazClassWithCtorNoDefaultArg implements BazInterface
{
	private HelloInterface $hello;
	public function __construct(HelloInterface $hello) {
		$this->hello = $hello;
	}
	
	public function returnHelloWorld() : string {
		return $this->hello->returnHello();
	}
}
