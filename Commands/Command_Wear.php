<?php

	class Command_Wear extends Command
	{
	
		public static function perform(&$actor, $args = null)
		{
		
			$item = $actor->getInventory()->getItemByInput($args);
			
			if(!($item instanceof Equipment))
				return Server::out($actor, "You cannot equip " . $item->getShort() . ".");
			
			if($item instanceof Item)
				return $actor->getEquipped()->equip($actor, $item);
			
			Server::out($actor, 'You have nothing like that in your inventory.');
		
		}
	
	}

?>
