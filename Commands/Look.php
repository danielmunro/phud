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
	class Look extends \Mechanics\Command
	{
		
		protected function __construct()
		{
			
			\Mechanics\Command::addAlias(__CLASS__, array('l', 'look'));
		}
		
		public static function perform(&$actor, $args = null)
		{
		
			if($args === null || sizeof($args) == 1) // The actor is looking
			{
				
				if(!$actor->getRoom()->getVisibility() && !\Mechanics\Affect::isAffecting($actor, \Mechanics\Affect::GLOW))
					return \Mechanics\Server::out($actor, "You can't see anything, it's so dark!");
				
				$doors = \Mechanics\Door::findByRoomId($actor->getRoom()->getId());
				
				\Mechanics\Server::out($actor, $actor->getRoom()->getTitle());
				\Mechanics\Server::out($actor, $actor->getRoom()->getDescription() . "\n");
				
				if(!empty($doors))
					foreach($doors as $door)
						if(!$door->getHidden($actor->getRoom()->getId()))
							\Mechanics\Server::out($actor, ucfirst($door->getLong($actor->getRoom()->getId())) . "\n");
				
				\Mechanics\Server::out($actor, 'Exits [' .
					($actor->getRoom()->getNorth() != 0 ? ' N ' : '') .
					($actor->getRoom()->getSouth() != 0 ? ' S ' : '') .
					($actor->getRoom()->getEast()  != 0 ? ' E ' : '') .
					($actor->getRoom()->getWest()  != 0 ? ' W ' : '') .
					($actor->getRoom()->getUp()    != 0 ? ' U ' : '') .
					($actor->getRoom()->getDown()  != 0 ? ' D ' : '') . ']');
				\Mechanics\ActorObserver::instance()->updateRoomChange($actor, 'looking');
				$items = $actor->getRoom()->getInventory()->getItems();
				
				if(is_array($items) && sizeof($items) > 0)
					foreach($items as $key => $item)
						\Mechanics\Server::out($actor, 
							ucfirst($item->getShort()) . ' is here.');
				
				return;
			}
			
			// Actor is looking at something... find out what it is
			$target = \Mechanics\ActorObserver::instance()->getActorByRoomAndInput($actor->getRoom()->getId(), $args);
			
			if(empty($target))
				$target = $actor->getRoom()->getInventory()->getItemByInput($args);
			
			if(empty($target))
				$target = $actor->getInventory()->getItemByInput($args);
			
			if(!empty($target))
				return \Mechanics\Server::out($actor, $target->lookDescribe());
			
			// Direction
			if(strpos($args[1], 'n') === 0)
				return self::lookDirection($actor, $actor->getRoom()->getNorth(), 'north');
			
			if(strpos($args[1], 's') === 0)
				return self::lookDirection($actor, $actor->getRoom()->getSouth(), 'south');
			
			if(strpos($args[1], 'e') === 0)
				return self::lookDirection($actor, $actor->getRoom()->getEast(), 'east');
			
			if(strpos($args[1], 'w') === 0)
				return self::lookDirection($actor, $actor->getRoom()->getWest(), 'west');
			
			if(strpos($args[1], 'u') === 0)
				return self::lookDirection($actor, $actor->getRoom()->getUp(), 'up');
			
			if(strpos($args[1], 'd') === 0)
				return self::lookDirection($actor, $actor->getRoom()->getDown(), 'down');
			
			Server::out($actor, 'Nothing is there.');
		}
		
		public static function lookDirection(&$actor, $room_id, $direction)
		{
			// Closed/locked door
			$door = \Mechanics\Door::findByRoomAndDirection($room_id, $direction);
			if($door instanceof \Mechanics\Door)
			{
				if($door->getHidden())
					return \Mechanics\Server::out($actor, Items\Item::getInstance($door->getHiddenItemId())->getLong());
				if($door->getDisposition() != \Mechanics\Door::DISPOSITION_OPEN)
					return \Mechanics\Server::out($actor, ucfirst($door->getLong($room_id)));
			}
			
			// No north
			if($room_id == 0)
				return \Mechanics\Server::out($actor, 'You see nothing ' . $direction . '.');
			// Something to the north
			else
				return \Mechanics\Server::out($actor, 'To the ' . $direction . ', you see: ' .
					\Mechanics\Room::find($room_id)->getTitle() . '.');
		}
	}
?>
