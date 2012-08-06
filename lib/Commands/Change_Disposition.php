<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Actors\User as aUser,
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
		$out = '';

		if(sizeof($args) > 1) {
			$furniture = $actor->getRoom()->getItemByInput($args[1]);
			if($furniture instanceof Furniture) {
				if($furniture->hasCapacity($actor)) {
					$furniture->addActor($actor);
					$out = "You ".$this->leaveDisposition($disposition)."begin ".$verb." on ".$furniture.".";
				} else {
					$out = ucfirst($furniture)." is full right now.";
				}
			} else if($furniture) {
				$out = "You can't ".$this->alias." on that!";
			} else {
				$out = "You can't find it.";
			}
		} else {
			$out = "You ".$this->leaveDisposition($disposition)."begin ".$verb.".";
		}

		if($actor instanceof aUser) {
			$actor->getClient()->writeLine($out);
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
