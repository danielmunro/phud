<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Items\Equipment as iEquipment;

class Wear extends Command
{
	protected $alias = 'wear';
	protected $dispositions = [Actor::DISPOSITION_STANDING];
	protected $min_argument_count = 1;
	protected $min_argument_fail = "Wear what?";

	public function perform(Actor $actor, iEquipment $equipment)
	{
		$actor->getEquipped()->equip($equipment);
	}

	protected function getArgumentsFromHints($actor, $args)
	{
		return [(new Arguments\Equipment($actor))->parse($actor, $args[1])];
	}
}
