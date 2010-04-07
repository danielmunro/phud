<?php

	class Command_Quit extends Command
	{
		
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
