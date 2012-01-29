<?php
namespace Commands;
use \Mechanics\Actor,
	\Mechanics\Alias,
	\Mechanics\Server,
	\Mechanics\Command\Command;

class Lock extends Command
{
	protected $dispositions = array(Actor::DISPOSITION_STANDING);

	protected function __construct()
	{
		self::addAlias('lock', $this);
	}

	public function perform(Actor $actor, $args = array())
	{
	
		if(sizeof($args) < 2)
			return Server::out($actor, 'Unlock what?');
	
		/**
		@TODO redo this crap
		$door = Command::findObjectByArgs(
								Door::findByRoomId($actor->getRoom()->getId()),
								$args[1]);
		
		if(empty($door))
			$door = Door::findByRoomAndDirection($actor->getRoom()->getId(), $args[1]);
		
		if(!($door instanceof Door))
			return Server::out($actor, 'Lock what?');
		
		if($door->getDisposition() == Door::DISPOSITION_OPEN)
			return Server::out($actor, "You must close the door first.");
		
		foreach($actor->getItems() as $item)
			if($item->getDoorUnlockId() == $door->getId())
			{
				$door->setDisposition('locked');
				return Server::out($actor, "You lock " . $door->getShort() . " with " . $item->getShort() . ".");
			}
		
		*/
		Server::out($actor, "You don't have the key!");
	}
}
?>
