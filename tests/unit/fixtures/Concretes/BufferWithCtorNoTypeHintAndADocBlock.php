<?php

declare(strict_types=1);

namespace Kraber\Test\Unit\Fixtures\Concretes;

use Kraber\Test\Unit\Fixtures\Contracts\BufferInterface;

class BufferWithCtorNoTypeHintAndADocBlock implements BufferInterface
{
	private mixed $buffer = 0;
	
	/**
	 * BufferWithCtorMixedArg constructor.
	 * @param string|null $value
	 */
	public function __construct($value) {
		$this->buffer = $value;
	}
	
	public function get() : mixed {
		return $this->buffer;
	}
}
