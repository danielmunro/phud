<?php
namespace Phud\Commands;
use Phud\Server,
	Phud\Affect,
	Phud\Actors\Actor,
	Phud\Actors\User as lUser,
	Phud\Door as mDoor,
	Phud\Room as mRoom;

class Look extends User
{
	protected $alias = 'look';
	protected $dispositions = [
		Actor::DISPOSITION_STANDING,
		Actor::DISPOSITION_SITTING
	];
	
	public function perform(lUser $user, $args = array())
	{
		if(!$args || sizeof($args) == 1) // The user is looking
		{
			$r = $user->getRoom();
			if(!$r->getVisibility() && !Affect::isAffecting($user, Affect::GLOW))
				return Server::out($user, "You can't see anything, it's so dark!");
			
			Server::out($user, $r->getTitle());
			Server::out($user, $r->getDescription() . "\n");

			$doors = $r->getDoors();
			foreach($doors as $i => $door) {
				Server::out($user, ucfirst($door).' is here.'.($i == sizeof($doors)-1 ? "\n" : ""));
			}

			$dir_str = '';
			foreach(['north' => $r->getNorth(), 'south' => $r->getSouth(), 'east' => $r->getEast(), 'west' => $r->getWest(), 'up' => $r->getUp(), 'down' => $r->getDown()] as $dir => $room) {
				if(isset($doors[$dir]) && $doors[$dir]->getDisposition() !== mDoor::DISPOSITION_OPEN) {
					continue;
				}
				if($room) {
					$dir_str .= ucfirst(substr($dir, 0, 1));
				}
			}

			Server::out($user, 'Exits ['.$dir_str.']');

			foreach($user->getRoom()->getItems() as $key => $item) {
				Server::out($user, ucfirst($item) . ' is here.');
			}
			
			foreach($user->getRoom()->getActors() as $a) {
				if($a !== $user) {
					$disposition = $a->getDisposition() === Actor::DISPOSITION_STANDING ? '' : ' '.$a->getDisposition();
					$post = '';
					if($a instanceof lUser) {
						$post = 'is '.$disposition.' here';
					}
					Server::out($user, ucfirst($a).$post.'.');
				}
			}
			return;
		}
		
		// Actor is looking at something... find out what it is
		$looking = implode(' ', array_slice($args, 1, sizeof($args)-1));
		$target = $user->getRoom()->getActorByInput($looking);
		
		if(empty($target))
			$target = $user->getRoom()->getItemByInput($looking);
		
		if(empty($target))
			$target = $user->getItemByInput($looking);
		
		if(!empty($target) && method_exists($target, 'getLong'))
			return Server::out($user, $target->getLong());
		
		// Direction
		if(strpos($args[1], 'n') === 0)
			return self::lookDirection($user, $user->getRoom()->getNorth(), 'north');
		
		if(strpos($args[1], 's') === 0)
			return self::lookDirection($user, $user->getRoom()->getSouth(), 'south');
		
		if(strpos($args[1], 'e') === 0)
			return self::lookDirection($user, $user->getRoom()->getEast(), 'east');
		
		if(strpos($args[1], 'w') === 0)
			return self::lookDirection($user, $user->getRoom()->getWest(), 'west');
		
		if(strpos($args[1], 'u') === 0)
			return self::lookDirection($user, $user->getRoom()->getUp(), 'up');
		
		if(strpos($args[1], 'd') === 0)
			return self::lookDirection($user, $user->getRoom()->getDown(), 'down');
		
		Server::out($user, 'Nothing is there.');
	}
	
	public static function lookDirection(&$user, $room, $direction)
	{
		/**
		// Closed/locked door
		$door = mDoor::findByRoomAndDirection($room, $direction);
		if($door instanceof mDoor)
		{
			if($door->getHidden())
				return Server::out($user, iItem::getInstance($door->getHiddenItemId())->getLong());
			if($door->getDisposition() != mDoor::DISPOSITION_OPEN)
				return Server::out($user, ucfirst($door->getLong($room)));
		}
		*/
		
		if(!($room instanceof mRoom))
			return Server::out($user, 'You see nothing ' . $direction . '.');
		else
			return Server::out($user, 'To the ' . $direction . ', you see: ' .
				$room->getTitle() . '.');
	}
}
?>
