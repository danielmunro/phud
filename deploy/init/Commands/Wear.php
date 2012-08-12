<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Items\Equipment as iEquipment;

class Wear extends Command
{
	protected $alias = 'wear';
	protected $dispositions = [Actor::DISPOSITION_STANDING];

	public function perform(Actor $actor, $args = [])
	{
		$item = $actor->getItemByInput($args[1]);
		
		if(!$item) {
			return $actor->notify('You have nothing like that in your inventory.');
		}
		
		if(!($item instanceof iEquipment)) {
			return $actor->notify("You cannot equip ".$item.".");
		}
		
		return $actor->getEquipped()->equip($item);
	}
}
