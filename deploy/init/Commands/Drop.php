<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Items\Item;

class Drop extends Command
{
	protected $alias = 'drop';
	protected $min_argument_count = 1;
	protected $min_argument_fail = "Drop what?";
	protected $dispositions = [
		Actor::DISPOSITION_STANDING,
		Actor::DISPOSITION_SITTING
	];

	public function perform(Actor $actor, Item $item)
	{
		$item->transferOwnership($actor, $actor->getRoom());
		$actor->notify("You drop ".$item.".");
	}

	protected function getArgumentsFromHints($actor, $args)
	{
		$item = implode(' ', array_slice($args, 1, sizeof($args)-1));
		return [(new Arguments\Item($actor))->parse($actor, $item)];
	}
}
