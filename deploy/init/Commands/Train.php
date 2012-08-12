<?php
namespace Phud\Commands;
use Phud\Actors\Trainer,
	Phud\Actors\Actor;

class Train extends User
{
	protected $alias = 'train';
	protected $dispositions = [Actor::DISPOSITION_STANDING];

	public function perform(Actor $actor, $args = [])
	{
		$args[1] = strtolower($args[1]);
		switch($args[1]) {
			case 'str':
			case 'int':
			case 'wis':
			case 'dex':
			case 'con':
			case 'cha':
				break;
			default:
				return $actor->notify("What stat would you like to train (str, int, wis, dex, con, cha)?");
		}

		$actors = $actor->getRoom()->getActors();
		foreach($actors as $a) {
			if($a instanceof Trainer) {
				$a->train($actor, $args[1]);
				return;
			}
		}
		$actor->notify("A trainer is not here to help you.");
	}
}
