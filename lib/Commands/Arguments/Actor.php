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
		$this->status = self::STATUS_INVALID;
		$actor->notify("No one is there.");
	}
}
