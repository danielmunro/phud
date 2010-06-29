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
	class Unlock extends \Mechanics\Command
	{
	
		protected function __construct()
		{
		
			\Mechanics\Command::addAlias(__CLASS__, array('un', 'unlock'));
		}
	
		public static function perform(&$actor, $args = null)
		{
		
			if(sizeof($args) < 2)
				return Server::out($actor, 'Unlock what?');
		
			$door = Command::findObjectByArgs(
									Door::findByRoomId($actor->getRoom()->getId()),
									$args[1]);
			
			if(empty($door))
				$door = Door::findByRoomAndDirection($actor->getRoom()->getId(), $args[1]);
			
			if(!($door instanceof Door))
				return Server::out($actor, 'Unlock what?');
			
			foreach($actor->getInventory()->getItems() as $item)
				if($item->getDoorUnlockId() == $door->getId())
				{
					$door->setDisposition('closed');
					return Server::out($actor, "You unlock " . $door->getShort() . " with " . $item->getShort() . ".");
				}
			
			Server::out($actor, "You don't have the key!");
		}
	}
?>
