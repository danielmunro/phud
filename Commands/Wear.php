<?php
	namespace Commands;
	class Wear extends \Mechanics\Command
	{
	
		protected function __construct()
		{
		
			\Mechanics\Command::addAlias(__CLASS__, 'wear');
		}
	
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
