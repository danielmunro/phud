<?php
namespace Phud\Commands;
use Phud\Server,
	Phud\Affect,
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
		$client = $user->getClient();
		if(!$args || sizeof($args) == 1) // The user is looking
		{
			$r = $user->getRoom();
			if(!$r->getVisibility() && !Affect::isAffecting($user, Affect::GLOW)) {
				return $client->write("You can't see anything, it's so dark!\r\n");
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
					$disposition = $a->getDisposition() === Actor::DISPOSITION_STANDING ? '' : ' '.$a->getDisposition();
					$post = '';
					if($a instanceof lUser) {
						$post = 'is '.$disposition.' here';
					}
					$message .= ucfirst($a->getShort()).$post.".\r\n";
				}
			}
			$client->write($message);
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
			return $client->write($target->getLong()."\r\n");
		
		// Direction
		foreach(Direction::getDirections() as $dir) {
			if(strpos($dir, $args[1]) === 0) {
				return self::lookDirection($client, $user->getRoom()->getDirection($dir), $dir);
			}
		}
		$client->write("Nothing is there.\r\n");
	}
	
	protected static function lookDirection($client, $room, $direction)
	{
		if(!($room instanceof mRoom))
			return $client->write("You see nothing ".$direction.".\r\n");
		else
			return $client->write("To the ".$direction.", you see: ".$room->getShort().".\r\n");
	}
}
?>
