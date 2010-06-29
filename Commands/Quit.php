<?php
	namespace Commands;
	class Quit extends \Mechanics\Command
	{
	
		protected function __construct()
		{
		
			\Mechanics\Command::addAlias(__CLASS__, 'quit');
		}
		
		public static function perform(&$actor, $args = null)
		{
			if($actor instanceof User)
			{
				$actor->save();
				Server::out($actor, "Good bye!\r\n");
				$actor->setClient(null);
				die;
			}
		}
	}
?>
