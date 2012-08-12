<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Door as mDoor,
	Phud\Commands\Command;

class Open extends Command
{
	protected $alias = 'open';
	protected $dispositions = [Actor::DISPOSITION_STANDING];

	public function perform(Actor $actor, $args = [])
	{
		if(sizeof($args) < 2) {
			return $actor->notify('Open what?');
		}
		
		$door = $actor->getRoom()->getDoorByInput($args[1]);
		
		if($door) {
			switch($door->getDisposition()) {
				case mDoor::DISPOSITION_CLOSED:
					$door->setDisposition(mDoor::DISPOSITION_OPEN);
					return $actor->notify('You open '.$door.'.');
				case mDoor::DISPOSITION_OPEN:
					return $actor->notify(ucfirst($door).' is already open.');
				case mDoor::DISPOSITION_LOCKED:
					return $actor->notify(ucfirst($door).' is locked.');
			}					
		}
		return $actor->notify("You can't open anything like that.");
	}
}
