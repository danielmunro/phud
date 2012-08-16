<?php
namespace Phud\Commands\Arguments;
use Phud\Actors\Actor as aActor;

class Actor extends Argument
{
	protected function parseArg(aActor $actor, $arg)
	{
		$target = $actor->getRoom()->getActorByInput($arg);
		if($target) {
			return $target;
		}
		$this->fail($actor, "No one is there.");
	}
}
