<?php
	namespace Commands;
	class Recall extends \Mechanics\Command
	{
	
		protected function __construct()
		{
		
			\Mechanics\Command::addAlias(__CLASS__, 'recall');
		}
	
		public static function perform(&$actor, $args = null)
		{
			$actor->setRoom(Room::find(1));
		}
	}
?>
