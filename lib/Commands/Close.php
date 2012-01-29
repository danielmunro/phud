<?php
namespace Commands;
use \Mechanics\Alias,
	\Mechanics\Actor,
	\Mechanics\Server,
	\Mechanics\Command\Command,
	\Mechanics\Door as mDoor;

class Close extends Command
{

	protected $alias = 'close';
	protected $dispositions = [Actor::DISPOSITION_STANDING];

	public function perform(Actor $actor, $args = array())
	{
		if(sizeof($args) == 1)
			return Server::out($actor, 'Close what?');
		
		$door = $actor->getRoom()->getDoorByInput($args[1]);
		
		if(!empty($door) && !$door->isHidden())
		{
			switch($door->getDisposition())
			{
				case mDoor::DISPOSITION_OPEN:
					$door->setDisposition(mDoor::DISPOSITION_CLOSED);
					$door->getParnterDoor()->setDisposition(mDoor::DISPOSITION_CLOSED);
					return Server::out($actor, 'You close '.$door.'.');
				case mDoor::DISPOSITION_CLOSED:
				case mDoor::DISPOSITION_LOCKED:
					return Server::out($actor, ucfirst($door) . ' is already closed.');
			}					
		}
		return Server::out($actor, "You can't close anything like that.");
	}
}
?>
