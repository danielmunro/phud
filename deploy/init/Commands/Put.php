<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Items\Container,
	Phud\Items\Item as iItem;

class Put extends Command
{
	protected $alias = 'put';
	protected $min_argument_count = 2;
	protected $min_argument_fail = "Put what, where?";
	protected $dispositions = [
		Actor::DISPOSITION_STANDING,
		Actor::DISPOSITION_SITTING
	];

	public function perform(Actor $actor, iItem $item, Container $container)
	{
		$item->transferOwnership($actor, $container);
		$actor->notify("You put ".$item." in ".$container.".");
	}

	protected function getArgumentsFromHints($actor, $args)
	{
		$s = sizeof($args);
		return [
			(new Arguments\Item($actor))->parse($actor, recombine($args, 1, $s-2)),
			(new Arguments\Container($actor))->parse($actor, $args[$s-1])
		];
	}
}
