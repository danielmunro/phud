<?php

	class Command_Gossip extends Command
	{
	
		public static function perform(&$actor, $args = null)
		{
		
			if(is_array($args))
			{
				array_shift($args);
				$message = implode(' ', $args);
			}
			else
				$message = $args;
		
			$actors = ActorObserver::instance()->getActors();
			
			foreach($actors as $a)
				if($a instanceof User)
					if($actor->getAlias() == $a->getAlias())
						Server::out($a, "You gossip, \"" . $message . "\"" . ($a instanceof User ? "\n\n" . $a->prompt() : ''), false);
					else
						Server::out($a, $a->getAlias(true) . " gossips, \"" . $message . "\"" . ($a instanceof User ? "\n\n" . $a->prompt() : ''), false);
		}
	}
?>
