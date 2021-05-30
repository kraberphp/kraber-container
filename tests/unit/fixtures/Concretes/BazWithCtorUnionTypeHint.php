<?php

declare(strict_types=1);

namespace Kraber\Test\Unit\Fixtures\Concretes;

use Kraber\Test\Unit\Fixtures\Contracts\{
	BazInterface,
	HelloInterface,
	WorldInterface
};

class BazWithCtorUnionTypeHint implements BazInterface
{
	private HelloInterface|WorldInterface $concrete;
	public function __construct(HelloInterface|WorldInterface $concrete) {
		$this->concrete = $concrete;
	}
	
	public function returnHelloWorld() : string {
		if ($this->concrete instanceof HelloInterface) {
			return $this->concrete->returnHello();
		}
		elseif ($this->concrete instanceof WorldInterface) {
			return $this->concrete->returnWorld();
		}
		
		return "";
	}
}
