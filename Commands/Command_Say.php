<?php

	class Command_Say extends Command
	{
	
		public static function perform(&$actor, $args = null)
		{
			
			$actors = ActorObserver::instance()->getActorsInRoom($actor->getRoom()->getId());
			
			if(is_array($args))
			{
				array_shift($args);
				$message = implode(' ', $args);
			}
			else if(is_string($args))
				$message = $args;
			
			foreach($actors as $a)
				if($a->getAlias() == $actor->getAlias())
					Server::out($a, "You say, \"" . $message ."\"");
				else
					Server::out($a, $actor->getAlias(true) . " says, \"" . $message . "\"");
		}
	}
?>
