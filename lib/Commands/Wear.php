<?php
namespace Commands;
use \Mechanics\Alias,
	\Mechanics\Actor,
	\Mechanics\Server,
	\Mechanics\Command\Command,
	\Mechanics\Equipment as mEquipment;

class Wear extends Command
{
	protected $alias = 'wear';
	protected $dispositions = [Actor::DISPOSITION_STANDING];

	public function perform(Actor $actor, $args = [])
	{
		$item = $actor->getItemByInput($args[1]);
		
		if(!$item) {
			return Server::out($actor, 'You have nothing like that in your inventory.');
		}
		
		if(!($item instanceof mEquipment)) {
			return Server::out($actor, "You cannot equip ".$item.".");
		}
		
		return $actor->getEquipped()->equip($item);
	}
}
?>
