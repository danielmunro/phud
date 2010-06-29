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
	class Buy extends Command
	{
	
		public static function perform(&$actor, $args = null)
		{
		
			if(sizeof($args) == 3)
				$target = ActorObserver::instance()->getActorByRoomAndInput($actor->getRoom()->getId(), array('', $args[2]));
			else
			{
				$targets = ActorObserver::instance()->getActorsInRoom($actor->getRoom()->getId());
				foreach($targets as $potential_target)
					if($potential_target instanceof Shopkeeper)
						$target = $potential_target;
			}
			
			if(!($target instanceof Actor))
				return Server::out($actor, "They are not here.");
			
			if(!($target instanceof Shopkeeper))
				return Server::out($actor, $target->getAlias(true) . " is not a shop keeper.");
			
			$item = $target->getInventory()->getItemByInput($args);
			
			if(!($item instanceof Item))
				return Command_Say::perform($target, $target->getNoItemMessage());
			
			if($actor->decreaseFunds($item->getValue()) === false)
				return Command_Say::perform($target, $target->getNotEnoughMoneyMessage());
			
			$item->copyTo($actor);
			
			Server::out($actor, "You buy " . $item->getShort() . " for " . $item->getValue() . " copper.");
			
		}
	}
?>
