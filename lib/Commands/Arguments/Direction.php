<?php
namespace Phud\Commands\Arguments;
use Phud\Actors\Actor as aActor,
	Phud\Room\Direction as rDirection;

class Direction extends Argument
{
	protected function parseArg(aActor $actor, $arg)
	{
		foreach(rDirection::getDirections() as $dir) {
			if(strpos($dir, $arg) === 0) {
				return $dir;
			}
		}
		$this->status = self::STATUS_INVALID;
		$actor->notify("Not a valid direction: ".$arg);
	}
}
