<?php
namespace Phud\Commands;
use Phud\Affect,
	Phud\Actors\Actor,
	Phud\Actors\User as lUser,
	Phud\Room\Room,
	Phud\Room\Direction;

class Look extends User
{
	protected $alias = 'look';
	protected $dispositions = [
		Actor::DISPOSITION_STANDING,
		Actor::DISPOSITION_SITTING
	];
	
	public function perform(lUser $user, $args = [])
	{
		if(!$args || sizeof($args) == 1) // The user is looking
		{
			$r = $user->getRoom();
			if(!$r->getVisibility() && !Affect::isAffecting($user, Affect::GLOW)) {
				return $user->notify("You can't see anything, it's so dark!\r\n");
			}

			$message =  $r->getShort().($user->isDM() ? " [".$r->getID()."]" : "")."\r\n".$r->getLong()."\r\n";

			$doors = $r->getDoors();
			foreach($doors as $i => $door) {
				$message .= ucfirst($door)." is here.".($i == sizeof($doors)-1 ? "\r\n" : "");
			}

			$dir_str = '';
			foreach(Direction::getDirections() as $dir) {
				if(isset($doors[$dir]) && $doors[$dir]->getDisposition() !== 'open') {
					continue;
				}
				if($r->getDirection($dir)) {
					$dir_str .= ucfirst($dir[0]);
				}
			}

			$message .= "Exits [".$dir_str."]\r\n";

			foreach($user->getRoom()->getItems() as $key => $item) {
				$message .= ucfirst($item)." is here.\r\n";
			}
			
			foreach($user->getRoom()->getActors() as $a) {
				if($a !== $user) {
					$disposition = $a->getDisposition();// === Actor::DISPOSITION_STANDING ? '' : ' '.$a->getDisposition();
					$post = '';
					if($a instanceof lUser) {
						$post = ' is '.$disposition.' here';
					}
					$message .= ucfirst($a).$post.".\r\n";
				}
			}
			$user->notify($message);
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
			return $user->notify($target->getLong()."\r\n");
		
		// Direction
		foreach(Direction::getDirections() as $dir) {
			if(strpos($dir, $args[1]) === 0) {
				return self::lookDirection($user, $user->getRoom()->getDirection($dir), $dir);
			}
		}
		$user->notify("Nothing is there.\r\n");
	}
	
	protected static function lookDirection($user, $room, $direction)
	{
		if(!($room instanceof mRoom))
			return $user->notify("You see nothing ".$direction.".\r\n");
		else
			return $user->notify("To the ".$direction.", you see: ".$room->getShort().".\r\n");
	}

	protected function getArgumentsFromHints($actor, $args)
	{
		return [$args];
	}
}
