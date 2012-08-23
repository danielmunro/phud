<?php
namespace Phud\Commands\Arguments;
use Phud\Room\Room;

class Actor extends Argument
{
	public function __construct(Room $search_in)
	{
		$this->search_in = $search_in;
	}

	public function parse($arg)
	{
		$target = $this->search_in->getActorByInput($arg);
		if($target) {
			return $target;
		}
		$this->fail("No one is there.");
	}
}
