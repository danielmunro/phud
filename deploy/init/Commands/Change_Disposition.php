<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Server,
	Phud\Items\Furniture;

abstract class Change_Disposition extends Command
{
	protected $dispositions = [
		Actor::DISPOSITION_STANDING,
		Actor::DISPOSITION_SITTING,
		Actor::DISPOSITION_SLEEPING
	];

	protected function changeDisposition(Actor $actor, $args, $verb)
	{
		$disposition = $actor->getDisposition();
		$actor->setDisposition($verb);
		if(sizeof($args) > 1) {
			$furniture = $actor->getRoom()->getItemByInput($args[1]);
			if($furniture instanceof Furniture) {
				if($furniture->hasCapacity($actor)) {
					$furniture->addActor($actor);
					return Server::out($actor, "You ".$this->leaveDisposition($disposition)."begin ".$verb." on ".$furniture.".");
				} else {
					return Server::out($actor, ucfirst($furniture)." is full right now.");
				}
			} else if($furniture) {
				return Server::out($actor, "You can't ".$this->alias." on that!");
			} else {
				return Server::out($actor, "You can't find it.");
			}
		} else {
			Server::out($actor, "You ".$this->leaveDisposition($disposition)."begin ".$verb.".");
		}
	}
	
	protected function leaveDisposition($disposition)
	{
		switch($disposition) {
			case 'sitting':
				return "stand up and ";
			case 'sleeping':
				return "wake up, stand up and ";
			default:
				return;
		}
	}
}

?>
