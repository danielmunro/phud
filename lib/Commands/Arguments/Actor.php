<?php
namespace Phud\Commands\Arguments;

class Actor extends Argument
{
	public function parse($arg)
	{
		$target = $actor->getRoom()->getActorByInput($arg);
		if($target) {
			return $target;
		}
		$this->fail("No one is there.");
	}
}
