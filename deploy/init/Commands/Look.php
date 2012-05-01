<?php
namespace Phud\Commands;
use Phud\Server,
	Phud\Affect,
	Phud\Actors\Actor,
	Phud\Actors\User as lUser,
	Phud\Room;

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
			
			Server::out($user, $r->getShort()."\n".$r->getLong()."\n");

			$doors = $r->getDoors();
			foreach($doors as $i => $door) {
				Server::out($user, ucfirst($door).' is here.'.($i == sizeof($doors)-1 ? "\n" : ""));
			}

			$dir_str = '';
			foreach(Room::getDirections() as $dir) {
				if(isset($doors[$dir]) && $doors[$dir]->getDisposition() !== 'open') {
					continue;
				}
				if($r->getDirection($dir)) {
					$dir_str .= ucfirst($dir[0]);
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
					Server::out($user, ucfirst($a->getShort()).$post.'.');
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
		foreach(Room::getDirections() as $dir) {
			if(strpos($dir, $args[1]) === 0) {
				return self::lookDirection($user, $user->getRoom()->getDirection($dir), $dir);
			}
		}
		Server::out($user, 'Nothing is there.');
	}
	
	public static function lookDirection(&$user, $room, $direction)
	{
		if(!($room instanceof mRoom))
			return Server::out($user, 'You see nothing ' . $direction . '.');
		else
			return Server::out($user, 'To the ' . $direction . ', you see: ' .
				$room->getShort() . '.');
	}
}
?>
