<?php
	namespace Commands;
	class Drop extends \Mechanics\Command
	{
	
		protected function __construct()
		{
		
			\Mechanics\Command::addAlias(__CLASS__, array('dr', 'drop'));
		}
	
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
