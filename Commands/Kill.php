<?php
	namespace Commands;
	class Kill extends \Mechanics\Command
	{
	
		protected function __construct()
		{
		
			\Mechanics\Command::addAlias(__CLASS__, array('k', 'm', 'kill', 'murder'));
		}
	
		public static function perform(&$actor, $args = null)
		{
			$target = $actor->getTarget();
			if($target instanceof Actor)
				Server::out($actor, "Whoa! Don't you think one is enough?");
			
			$target = ActorObserver::instance()->getActorByRoomAndInput($actor->getRoom()->getId(), $args);
			
			if(!($target instanceof Actor))
			{
				Server::out($actor, 'Nothing is here.');
				return;
			}
						
			$actor->addFighter($target);
			return;
		}
	
	}

?>
