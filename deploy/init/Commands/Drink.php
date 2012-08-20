<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Items\Item as mItem,
	Phud\Items\Drink as iDrink,
	\InvalidArgumentException;

class Drink extends User
{
	protected $alias = 'drink';
	protected $dispositions = [
		Actor::DISPOSITION_STANDING,
		Actor::DISPOSITION_SITTING
	];
	
	public function perform(Actor $actor, iDrink $drink)
	{
		if($drink->drink($actor)) {
			$actor->notify("You drink ".$drink->getContents()." from ".$drink.".");
		} else {
			$actor->notify("There's no ".$drink->getContents()." left.");
		}
	}

	protected function getArgumentsFromHints($actor, $args)
	{
		return [(new Arguments\Drink($actor))->parse($actor, sizeof($args) === 2 ? $args[1] : null)];
	}
}
