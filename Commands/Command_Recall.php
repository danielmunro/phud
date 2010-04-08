<?php

	class Command_Recall extends Command
	{
	
		public static function perform(&$actor, $args = null)
		{
			$actor->setRoom(Room::find(1));
		}
	}
?>
