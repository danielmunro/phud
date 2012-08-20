<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Items\Equipment as iEquipment;

class Remove extends Command
{
	protected $alias = 'remove';
	protected $min_argument_count = 1;
	protected $min_argument_fail = "Remove what?";
	protected $dispositions = [Actor::DISPOSITION_STANDING];

	public function perform(Actor $actor, iEquipment $equipment)
	{
		$actor->getEquipped()->remove($equipment);
		$actor->notify('You remove ' . $equipment->getShort() . '.');
	}

	protected function getArgumentsFromHints($actor, $args)
	{
		return [(new Arguments\Equipment($actor))->parse($actor, $args[1])];
	}
}
