<?php
namespace Phud\Commands\Arguments;
use Phud\Actors\Actor as aActor;

class Number extends Argument
{
	protected function parseArg(aActor $actor, $arg)
	{
		if(is_numeric($arg)) {
			return $arg;
		}
		$this->status = self::STATUS_INVALID;
		$actor->notify("That does not seem like a valid modifier.");
	}
}
