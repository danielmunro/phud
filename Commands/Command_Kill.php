<?php

	class Command_Kill extends Command
	{
	
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
