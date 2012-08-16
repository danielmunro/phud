<?php
namespace Phud\Commands\Arguments;
use Phud\Actors\Actor as aActor,
	\ReflectionClass;

class Attribute extends Argument
{
	protected function parseArg(aActor $actor, $arg)
	{
		$ref = new ReflectionClass('Phud\\Attributes');
		if($ref->hasProperty($arg)) {
			return $arg;
		}
		$this->fail($actor, "That is not a valid attribute.");
	}
}
