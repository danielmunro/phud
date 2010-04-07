<?php

	class Command_Wear extends Command
	{
	
		public static function perform(&$actor, $args = null)
		{
		
			$item = $actor->getInventory()->getItemByInput($args);
			
			if($item instanceof Item && !($item instanceof Equipable))
				return Server::out($actor, "You cannot equip " . $item->getShort() . ".");
			
			if($item instanceof Item)
				$actor->getEquipment()->equip($actor, $item);
			
			Server::out($actor, 'You have nothing like that in your inventory.');
		
		}
	
	}

?>
