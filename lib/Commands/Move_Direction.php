<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Actors\User as aUser,
	Phud\Server,
	Phud\Room,
	Phud\Door,
	Phud\Commands\Command;

abstract class Move_Direction extends Command
{
	protected $dispositions = [Actor::DISPOSITION_STANDING];

	public function perform(Actor $actor, $direction)
	{
		if($actor->getTarget()) {
			return Server::out($actor, 'You cannot leave a fight!');
		}

		$room_id = $actor->getRoom()->getDirection($direction);
		$room = Room::find($room_id);
		if($room instanceof Room) {
			$doors = $actor->getRoom()->getDoors();
			if(isset($doors[$direction]) && $doors[$direction]->getDisposition() !== Door::DISPOSITION_OPEN) {
				return Server::out($actor, ucfirst($doors[$direction]).' is not open.');
			}
			$movement_cost = 1;
			$actor->fire('moved', $movement_cost, $room);
			if($actor->getAttribute('movement') >= $movement_cost) {
				$actor->modifyAttribute('movement', -($movement_cost));
				$actor->getRoom()->announce($actor, ucfirst($actor).' '.$actor->getRace()['lookup']->getMoveVerb().' '.$direction.'.');
				$actor->setRoom($room);
				if($actor instanceof aUser) {
					$look = Command::lookup('look');
					$look['lookup']->perform($actor);
				}
				$actor->getRoom()->announce($actor, ucfirst($actor).' has arrived.');
				return;
			}
			Server::out($actor, 'You are too exhausted.');
		} else {
			Server::out($actor, 'Alas, you cannot go that way.');
		}
	}
}
?>
