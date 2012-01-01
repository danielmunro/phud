<?php

	/**
	 *
	 * Phud - a PHP implementation of the popular multi-user dungeon game paradigm.
     * Copyright (C) 2009 Dan Munro
	 * 
     * This program is free software; you can redistribute it and/or modify
     * it under the terms of the GNU General Public License as published by
     * the Free Software Foundation; either version 2 of the License, or
     * (at your option) any later version.
	 * 
     * This program is distributed in the hope that it will be useful,
     * but WITHOUT ANY WARRANTY; without even the implied warranty of
     * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     * GNU General Public License for more details.
	 * 
     * You should have received a copy of the GNU General Public License along
     * with this program; if not, write to the Free Software Foundation, Inc.,
     * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
	 *
	 * Contact Dan Munro at dan@danmunro.com
	 * @author Dan Munro
	 * @package Phud
	 *
	 */
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
		
		protected $dispositions = [
			Actor::DISPOSITION_STANDING,
			Actor::DISPOSITION_SITTING
		];
		
		protected function __construct()
		{
			self::addAlias('look', $this);
		}
		
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
					($user->getRoom()->getNorth() >= 0 ? ' N ' : '') .
					($user->getRoom()->getSouth() >= 0 ? ' S ' : '') .
					($user->getRoom()->getEast()  >= 0 ? ' E ' : '') .
					($user->getRoom()->getWest()  >= 0 ? ' W ' : '') .
					($user->getRoom()->getUp()    >= 0 ? ' U ' : '') .
					($user->getRoom()->getDown()  >= 0 ? ' D ' : '') . ']');
				$items = $user->getRoom()->getInventory()->getItems();
				
				if(is_array($items) && sizeof($items) > 0)
					foreach($items as $key => $item)
						Server::out($user, 
							ucfirst($item->getShort()) . ' is here.');
				
				
				$people = $user->getRoom()->getActors();
				foreach($people as $a) {
					if($a !== $user) {
						Server::out($user, ucfirst($a).' is '.$a->getDisposition().' here.');
					}
				}
				return;
			}
			
			// Actor is looking at something... find out what it is
			$looking = implode(' ', array_slice($args, 1, sizeof($args)-1));
			$target = $user->getRoom()->getActorByInput($looking);
			
			if(empty($target))
				$target = $user->getRoom()->getInventory()->getItemByInput($looking);
			
			if(empty($target))
				$target = $user->getInventory()->getItemByInput($looking);
			
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
		
		public static function lookDirection(&$user, $room_id, $direction)
		{
			// Closed/locked door
			$door = mDoor::findByRoomAndDirection($room_id, $direction);
			if($door instanceof mDoor)
			{
				if($door->getHidden())
					return Server::out($user, iItem::getInstance($door->getHiddenItemId())->getLong());
				if($door->getDisposition() != mDoor::DISPOSITION_OPEN)
					return Server::out($user, ucfirst($door->getLong($room_id)));
			}
			
			if($room_id == 0)
				return Server::out($user, 'You see nothing ' . $direction . '.');
			else
				return Server::out($user, 'To the ' . $direction . ', you see: ' .
					mRoom::find($room_id)->getTitle() . '.');
		}
	}
?>
