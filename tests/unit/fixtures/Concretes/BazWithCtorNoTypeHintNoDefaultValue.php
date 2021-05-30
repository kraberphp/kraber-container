<?php

declare(strict_types=1);

namespace Kraber\Test\Unit\Fixtures\Concretes;

use Kraber\Test\Unit\Fixtures\Contracts\BazInterface;

class BazWithCtorNoTypeHintNoDefaultValue implements BazInterface
{
	private string $str = "";
	public function __construct($str) {
		$this->str = $str;
	}
	
	public function returnHelloWorld() : string {
		return $this->str;
	}
}
