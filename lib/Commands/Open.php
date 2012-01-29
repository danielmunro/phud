<?php
namespace Commands;
use \Mechanics\Actor,
	\Mechanics\Alias,
	\Mechanics\Server,
	\Mechanics\Door as mDoor,
	\Mechanics\Command\Command;

class Open extends Command
{
	protected $alias = 'open';
	protected $dispositions = [Actor::DISPOSITION_STANDING];

	public function perform(Actor $actor, $args = array())
	{
		if(sizeof($args) < 2)
			return Server::out($actor, 'Open what?');
		
		$door = $actor->getRoom()->getDoorByInput($args[1]);
		
		if(!empty($door) && !$door->isHidden())
		{
			switch($door->getDisposition())
			{
				case mDoor::DISPOSITION_CLOSED:
					$door->setDisposition(mDoor::DISPOSITION_OPEN);
					$door->getParnterDoor()->setDisposition(mDoor::DISPOSITION_OPEN);
					return Server::out($actor, 'You open '.$door.'.');
				case mDoor::DISPOSITION_OPEN:
					return Server::out($actor, ucfirst($door).' is already open.');
				case mDoor::DISPOSITION_LOCKED:
					return Server::out($actor, ucfirst($door).' is locked.');
			}					
		}
		return Server::out($actor, "You can't open anything like that.");
	}
}
?>
