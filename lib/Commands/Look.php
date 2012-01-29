<?php
namespace Commands;
use \Mechanics\Alias,
	\Mechanics\Server,
	\Mechanics\Affect,
	\Mechanics\Actor,
	\Mechanics\Command\User,
	\Living\User as lUser,
	\Items\Item as iItem,
	\Mechanics\Door as mDoor,
	\Mechanics\Room as mRoom;

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
			if(!$user->getRoom()->getVisibility() && !Affect::isAffecting($user, Affect::GLOW))
				return Server::out($user, "You can't see anything, it's so dark!");
			
			Server::out($user, $user->getRoom()->getTitle());
			Server::out($user, $user->getRoom()->getDescription() . "\n");
			
			$doors = $user->getRoom()->getDoors();
			array_walk(
				$doors,
				function($door) use ($user)
				{
					if($door)
					{
						$display = true;
						if($door->isHidden())
							$display = rand(0, 3) === 3 ? true : false;
						if($display)
							Server::out($user, ucfirst($door->getLong()) . "\n");
					}
				}
			);
			
			Server::out($user, 'Exits [' .
				($user->getRoom()->getNorth() instanceof mRoom ? ' N ' : '') .
				($user->getRoom()->getSouth() instanceof mRoom ? ' S ' : '') .
				($user->getRoom()->getEast()  instanceof mRoom ? ' E ' : '') .
				($user->getRoom()->getWest()  instanceof mRoom ? ' W ' : '') .
				($user->getRoom()->getUp()    instanceof mRoom ? ' U ' : '') .
				($user->getRoom()->getDown()  instanceof mRoom ? ' D ' : '') . ']');
			$items = $user->getRoom()->getItems();
			
			if(is_array($items) && sizeof($items) > 0)
				foreach($items as $key => $item)
					Server::out($user, 
						ucfirst($item->getShort()) . ' is here.');
			
			
			$people = $user->getRoom()->getActors();
			foreach($people as $a) {
				if($a !== $user) {
					$disposition = $a->getDisposition() === Actor::DISPOSITION_STANDING ? '' : ' '.$a->getDisposition();
					Server::out($user, ucfirst($a).' is'.$disposition.' here.');
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
		
		if(!empty($target))
			return Server::out($user, $target->lookDescribe());
		
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
		// Closed/locked door
		$door = mDoor::findByRoomAndDirection($room, $direction);
		if($door instanceof mDoor)
		{
			if($door->getHidden())
				return Server::out($user, iItem::getInstance($door->getHiddenItemId())->getLong());
			if($door->getDisposition() != mDoor::DISPOSITION_OPEN)
				return Server::out($user, ucfirst($door->getLong($room)));
		}
		
		if(!($room instanceof mRoom))
			return Server::out($user, 'You see nothing ' . $direction . '.');
		else
			return Server::out($user, 'To the ' . $direction . ', you see: ' .
				$room->getTitle() . '.');
	}
}
?>
