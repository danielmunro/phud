<?php
namespace Phud\Commands\Arguments;

class Number extends Argument
{
	public function parse($arg)
	{
		if(is_numeric($arg)) {
			return $arg;
		}
		$this->fail("That does not seem like a valid modifier.");
	}
}
