<?php
namespace Commands;
use \Mechanics\Actor,
	\Mechanics\Server,
	\Mechanics\Alias,
	\Mechanics\Room as mRoom,
	\Mechanics\Door as mDoor,
	\Mechanics\Event\Event,
	\Mechanics\Event\Subscriber,
	\Mechanics\Command\Command,
	\Living\User;

abstract class Move_Direction extends Command
{
	protected $dispositions = [Actor::DISPOSITION_STANDING];

	public function perform(Actor $actor, $args = [])
	{
		if($actor->getTarget()) {
			return Server::out($actor, 'You cannot leave a fight!');
		}

		$room = mRoom::find($args[0]);
		if($room instanceof mRoom) {
			$doors = $actor->getRoom()->getDoors();
			$direction = $args[1];
			if(isset($doors[$direction]) && $doors[$direction]->getDisposition() !== mDoor::DISPOSITION_OPEN) {
				return Server::out($actor, ucfirst($doors[$direction]).' is not open.');
			}
			$movement_cost = 1;
			$actor->fire(Event::EVENT_MOVED, $movement_cost, $room);
			if($actor->getAttribute('movement') >= $movement_cost) {
				$actor->modifyAttribute('movement', -($movement_cost));
				$actor->getRoom()->announce($actor, ucfirst($actor).' '.$actor->getRace()['lookup']->getMoveVerb().' '.$args[1].'.');
				$actor->setRoom($room);
				if($actor instanceof User) {
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
