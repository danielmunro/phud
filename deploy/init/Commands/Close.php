<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Door;

class Close extends Command
{
	protected $alias = 'close';
	protected $dispositions = [Actor::DISPOSITION_STANDING];

	public function perform(Actor $actor, $args = [])
	{
		if(sizeof($args) < 2) {
			return $actor->notify('Close what?');
		}
		
		$door = $actor->getRoom()->getDoorByInput($args[1]);
		
		if($door) {
			switch($door->getDisposition()) {
				case Door::DISPOSITION_OPEN:
					$door->setDisposition(Door::DISPOSITION_CLOSED);
					return $actor->notify('You close '.$door.'.');
				case Door::DISPOSITION_CLOSED:
				case Door::DISPOSITION_LOCKED:
					return $actor->notify(ucfirst($door).' is already closed.');
			}					
		}
		return $actor->notify("You can't close anything like that.");
	}
}
