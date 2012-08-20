<?php
namespace Phud\Commands;
use Phud\Actors\Trainer,
	Phud\Actors\Actor;

class Train extends User
{
	protected $alias = 'train';
	protected $dispositions = [Actor::DISPOSITION_STANDING];
	protected $min_argument_count = 1;
	protected $min_argument_fail = "What stat would you like to train (str, int, wis, dex, con, cha)?";
	private $valid_args = ['str', 'int', 'wis', 'dex', 'con', 'cha'];

	public function perform(Actor $actor, $attribute, Trainer $trainer)
	{
		$trainer->train($actor, $attribute);
	}

	protected function getArgumentsFromHints($actor, $args)
	{
		if(in_array($args[1], $this->valid_args)) {
			$s = sizeof($args);
			$trainer = null;
			if($s === 2) {
				foreach($actor->getRoom()->getActors() as $a) {
					if($a instanceof Trainer) {
						$trainer = $a;
						break;
					}
				}
			} else {
				$trainers = $this->actor->getUsablesByInput($this->actor->getRoom()->getActors(), $args[2]);
				$trainer = $trainers ? $trainers[0] : null;
			}
		} else {
			$actor->notify("That is not a valid attribute to train.");
			throw new \InvalidArgumentException();
		}
		if(!$trainer) {
			$actor->notify("A trainer is not here to help you.");
			throw new \InvalidArgumentException();
		}
		return [$args[1], $trainer];
	}
}
