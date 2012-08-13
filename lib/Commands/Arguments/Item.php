<?php
namespace Phud\Commands\Arguments;
use Phud\Actors\Actor as aActor;

class Item extends Argument
{
	protected function parseArg(aActor $actor, $arg)
	{
		$this->status = self::STATUS_INVALID;
	}
}
