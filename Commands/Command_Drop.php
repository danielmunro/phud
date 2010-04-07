<?php

	class Command_Drop extends Command
	{
	
		public static function perform(&$actor, $args = null)
		{
		
			$item = $actor->getInventory()->getItemByInput($args);
			
			if(!($item instanceof Item))
				return Server::out($actor, "You do not have anything like that.");
			
			$item->transferOwnership($actor, $actor->getRoom());

			Server::out($actor, "You drop " . $item->getShort() . ".");
		}
	}
?>
