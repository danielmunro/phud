<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Items\Item,
	Phud\Items\Food,
	\InvalidArgumentException;

class Eat extends User
{
	protected $alias = 'eat';
	protected $min_argument_count = 1;
	protected $min_argument_fail = "Eat what?";
	protected $dispositions = [
		Actor::DISPOSITION_STANDING,
		Actor::DISPOSITION_SITTING
	];

	public function perform(Actor $actor, Food $food)
	{
		$actor->notify("You eat ".$food.".");
		$actor->consume($food);
	}

	protected function getArgumentsFromHints($actor, $args)
	{
		return [(new Arguments\Food($actor))->parse($actor, $args[1])];
	}
}
