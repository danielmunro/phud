<?php
	namespace Commands;
	class Quest extends \Mechanics\Command
	{
	
		protected function __construct()
		{
		
			\Mechanics\Command::addAlias(__CLASS__, array('q', 'quest'));
		}
	
		public static function perform(&$actor, $args = null)
		{
			
			$action = $args[1];
			$target = null;
			
			if(sizeof($args) == 3)
			{
				$target = ActorObserver::findByRoomAndInput($actor->getRoom()->getId(), array('', $args[2]));
				if(!($target instanceof Questmaster))
					return Server::out($actor, "You don't see them anywhere.");
			}
			
			if(!($target instanceof Questmaster))
				foreach(ActorObserver::instance()->getActorsInRoom($actor->getRoom()->getId()) as $t)
					if($t instanceof Questmaster)
						$target = $t;
			
			if(!($target instanceof Questmaster))
				return Server::out($actor, "There are no " . Tag::apply('Questmasters') . "here.");
			
			if(strpos('info', $action) === 0)
				return $target->questInfo($actor);

			if(strpos('accept', $action) === 0)
				return $target->questAccept($actor);
			
			if(strpos('done', $action) === 0)
				return $target->questDone($actor);
			
			return Server::out($actor, "There is no quest action like that. Try help quest.");
		}
	}
?>
