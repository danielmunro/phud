<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Server,
	Phud\Door;

class Close extends Command
{

	protected $alias = 'close';
	protected $dispositions = [Actor::DISPOSITION_STANDING];

	public function perform(Actor $actor, $args = [])
	{
		if(sizeof($args) < 2) {
			return Server::out($actor, 'Close what?');
		}
		
		$door = $actor->getRoom()->getDoorByInput($args[1]);
		
		if($door) {
			switch($door->getDisposition()) {
				case Door::DISPOSITION_OPEN:
					$door->setDisposition(Door::DISPOSITION_CLOSED);
					return Server::out($actor, 'You close '.$door.'.');
				case Door::DISPOSITION_CLOSED:
				case Door::DISPOSITION_LOCKED:
					return Server::out($actor, ucfirst($door) . ' is already closed.');
			}					
		}
		return Server::out($actor, "You can't close anything like that.");
	}
}
?>
