<?php
namespace Commands;
use \Mechanics\Actor,
	\Items\Furniture,
	\Mechanics\Server,
	\Mechanics\Command\Command;

class Sit extends Command
{
	protected $alias = 'sit';
	protected $dispositions = [
		Actor::DISPOSITION_STANDING,
		Actor::DISPOSITION_SITTING,
		Actor::DISPOSITION_SLEEPING
	];

	public function perform(Actor $actor, $args = [])
	{
		$disposition = $actor->getDisposition();
		$actor->setDisposition('sitting');
		if(sizeof($args) > 1) {
			$furniture = $actor->getRoom()->getItemByInput($args[1]);
			if($furniture instanceof Furniture) {
				if($furniture->hasCapacity($actor)) {
					$furniture->addActor($actor);
					return Server::out($actor, "You ".$this->leaveDisposition($disposition)."begin sitting on ".$furniture.".");
				} else {
					return Server::out($actor, ucfirst($furniture)." is full right now.");
				}
			} else if($furniture) {
				return Server::out($actor, "You can't sit on that!");
			} else {
				return Server::out($actor, "You can't find it.");
			}
		} else {
			Server::out($actor, "You ".$this->leaveDisposition($disposition)."begin sitting.");
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
