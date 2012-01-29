<?php
namespace Commands;
use \Mechanics\Actor,
	\Mechanics\Server,
	\Mechanics\Alias,
	\Mechanics\Room as mRoom,
	\Mechanics\Door as mDoor,
	\Mechanics\Event\Event,
	\Mechanics\Event\Subscriber,
	\Mechanics\Command\Command;

abstract class Move_Direction extends Command
{
	protected $dispositions = array(Actor::DISPOSITION_STANDING);

	public function perform(Actor $actor, $args = [])
	{
		if($actor->getDisposition() === Actor::DISPOSITION_SITTING)
			return Server::out($actor, "You need to stand up to do that.");
		if($actor->getDisposition() === Actor::DISPOSITION_SLEEPING)
			return Server::out($actor, "You can't do anything, you're sleeping!");
	
		if($actor->getTarget())
			return Server::out($actor, 'You cannot leave a fight!');
		
		if($args[0] instanceof mRoom)
		{
			$room = $args[0];
			$door = $room->getDoor($args[1]);
			if($door instanceof mDoor)
			{
				if($door->isHidden())
					return Server::out($actor, 'Alas, you cannot go that way.');
				if($door->getDisposition() != mDoor::DISPOSITION_OPEN)
					return Server::out($actor, ucfirst($door->getShort()) . ' is ' . $door->getDisposition() . '.');
			}
			$movement_cost = 1;
			$actor->fire(Event::EVENT_MOVED, $movement_cost, $room);
			if($actor->getAttribute('movement') >= $movement_cost || $actor->getLevel() > Actor::MAX_LEVEL) {
				$actor->modifyAttribute('movement', -($movement_cost));
				$actor->getRoom()->announce($actor, ucfirst($actor).' '.$actor->getRace()['lookup']->getMoveVerb() . ' ' . $args[1] . '.');
				$actor->setRoom($room);
				if($actor instanceof \Living\User) {
					$look = Command::lookup('look');
					$look['lookup']->perform($actor);
				}
				$actor->getRoom()->announce($actor, ucfirst($actor).' has arrived.');
				
				return;
			}
			Server::out($actor, 'You are too exhausted.');
		}
		else
			Server::out($actor, 'Alas, you cannot go that way.');
	
	}

}

?>
