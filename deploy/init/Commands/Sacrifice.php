<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Items\Item as iItem,
	Phud\Items\Corpse;

class Sacrifice extends Command
{
	protected $alias = 'sacrifice';
	protected $min_argument_count = 1;
	protected $min_argument_fail = "What do you want to sacrifice?";
	protected $dispositions = [Actor::DISPOSITION_STANDING];

	public function perform(Actor $actor, iItem $item)
	{
		$actor->getRoom()->removeItem($item);
		$copper = max(1, $item->getLevel()*3);
		if(!($item instanceof Corpse)) {
			$copper = min($copper, $item->getValue());
		}
		$actor->notify("Mojo finds ".$item." pleasing and rewards you.");
		$actor->getRoom()->announce($actor, $actor." sacrifices ".$item." to Mojo.");
		$actor->modifyCurrency('copper', $copper);
	}

	protected function getArgumentsFromHints($actor, $args)
	{
		return [(new Arguments\Item($actor->getRoom()))->parse($actor, $args[1])];
	}
}
