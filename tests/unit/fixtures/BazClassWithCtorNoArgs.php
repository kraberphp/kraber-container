<?php

declare(strict_types=1);

namespace Kraber\Test\Unit\Fixtures;

class BazClassWithCtorNoArgs implements BazInterface
{
	private string $str = "";
	public function __construct() {
		$this->str = "Hello world !";
	}
	
	public function returnHelloWorld() : string {
		return $this->str;
	}
}
