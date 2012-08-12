<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Room,
	Phud\Direction;

class Flee extends Command
{
	protected $alias = 'flee';
	protected $dispositions = [Actor::DISPOSITION_STANDING];

	public function perform(Actor $fighter, $args = [])
	{
		$target = $fighter->getTarget();
		
		// sanity check
		if(!$target) {
			return $fighter->notify("Flee from who?");
		}

		// remove targets
		$target->setTarget(null);
		$fighter->setTarget(null);
		
		// build a list of directions and randomize it
		$r = $fighter->getRoom();
		$directions = [];
		foreach(Direction::getDirections() as $direction) {
			$directions[] = [$direction, $r->getDirection($direction)];
		}
		shuffle($directions);

		// attempt to flee in a direction at random
		foreach($directions as $direction) {
			if($direction[1]) {
				Command::lookup($direction[0])->perform($fighter);
				$fighter->notify("You run scared!");
				return;
			}
		}

		// an exitless room
		$fighter->notify("You don't see anywhere to flee!");
	}
}
