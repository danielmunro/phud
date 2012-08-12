<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Items\Equipment,
	Phud\Commands\Command;

class Remove extends Command
{
	protected $alias = 'remove';
	protected $dispositions = [
		Actor::DISPOSITION_STANDING,
		Actor::DISPOSITION_SITTING
	];

	public function perform(Actor $actor, $args = [])
	{
		$equipment = $actor->getEquipped()->getItemByInput($args);
		
		if($equipment instanceof Equipment)
		{
			$actor->getEquipped()->remove($equipment);
			$actor->notify('You remove ' . $equipment->getShort() . '.');
		}
		else
			return $actor->notify('You are not wearing anything like that.');
	}
}
