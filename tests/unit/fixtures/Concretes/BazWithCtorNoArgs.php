<?php

declare(strict_types=1);

namespace Kraber\Test\Unit\Fixtures\Concretes;

use Kraber\Test\Unit\Fixtures\Contracts\BazInterface;

class BazWithCtorNoArgs implements BazInterface
{
	private string $str = "";
	public function __construct() {
		$this->str = "Hello world !";
	}
	
	public function returnHelloWorld() : string {
		return $this->str;
	}
}
