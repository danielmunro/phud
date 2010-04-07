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

	class Command_Give extends Command
	{
	
		public static function perform(&$actor, $args = null)
		{
		
			$item = $actor->getInventory()->getItemByInput($args);
			array_shift($args);
			
			$target = ActorObserver::instance()->getActorByRoomAndInput($actor->getRoom()->getId(), $args);
			
			if(empty($item))
				return Server::out($actor, "You don't appear to have that.");
			
			if(!($target instanceof Actor))
				return Server::out($actor, "You don't see them here.");
			
			$actor->getInventory()->remove($item);
			$target->getInventory()->add($item);
			Server::out($actor, "You give " . $item->getShort() . " to " . $target->getAlias() . ".");
			Server::out($target, $actor->getAlias() . " has given " . $item->getShort() . " to you.");
		
		}
	
	}

?>
