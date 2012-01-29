<?php
namespace Commands;
use \Mechanics\Alias,
	\Mechanics\Door as mDoor,
	\Mechanics\Actor,
	\Mechanics\Server,
	\Mechanics\Command\Command;

class Unlock extends Command
{
	protected $alias = 'unlock';
	protected $dispositions = [Actor::DISPOSITION_STANDING];

	public function perform(Actor $actor, $args = array())
	{
		/**
		@TODO redo this crap
		if(sizeof($args) < 2)
			return Server::out($actor, 'Unlock what?');
	
		$door = Command::findObjectByArgs(
								Door::findByRoomId($actor->getRoom()->getId()),
								$args[1]);
		
		if(empty($door))
			$door = Door::findByRoomAndDirection($actor->getRoom()->getId(), $args[1]);
		
		if(!($door instanceof Door))
			return Server::out($actor, 'Unlock what?');
		
		foreach($actor->getItems() as $item)
			if($item->getDoorUnlockId() == $door->getId())
			{
				$door->setDisposition('closed');
				return Server::out($actor, "You unlock " . $door->getShort() . " with " . $item->getShort() . ".");
			}
		
		*/
		Server::out($actor, "You don't have the key!");
	}
}
?>
