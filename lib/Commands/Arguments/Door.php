<?php
namespace Phud\Commands\Arguments;
use Phud\Room\Door as rDoor;

class Door extends Argument
{
	public function parse($arg)
	{
		$door = $actor->getRoom()->getDoorByInput($arg);
		if($door) {
			return $door;
		}
		$this->fail("Nothing is there.");
	}
}
