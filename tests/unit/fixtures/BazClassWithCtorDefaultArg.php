<?php

declare(strict_types=1);

namespace Kraber\Test\Unit\Fixtures;

class BazClassWithCtorDefaultArg implements BazInterface
{
	private string $str = "";
	public function __construct($str = "Hello world !") {
		$this->str = $str;
	}
	
	public function returnHelloWorld() : string {
		return $this->str;
	}
}
