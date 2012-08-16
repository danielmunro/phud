<?php
namespace Phud\Commands\Arguments;
use Phud\Actors\Actor as aActor,
	Phud\Room\Door as rDoor;

class Door extends Argument
{
	protected function parseArg(aActor $actor, $arg)
	{
		$door = $actor->getRoom()->getDoorByInput($arg);
		if($door) {
			return $door;
		}
		$this->fail("Nothing is there.");
	}
}
