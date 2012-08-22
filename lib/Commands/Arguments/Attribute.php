<?php
namespace Phud\Commands\Arguments;

class Attribute extends Argument
{
	public function parse($arg)
	{
		$ref = new \ReflectionClass('Phud\\Attributes');
		if($ref->hasProperty($arg)) {
			return $arg;
		}
		$this->fail("That is not a valid attribute.");
	}
}
