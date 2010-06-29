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
	namespace Mechanics;
	abstract class Move_Direction extends Command
	{

		public static function perform(&$actor, $args = null)
		{
		
			if($actor->getTarget() instanceof Actor)
				return Server::out($actor, 'You cannot leave a fight!');
			
			$door = Door::findByRoomAndDirection($actor->getRoom()->getId(), $args[1]);
			
			if($door instanceof Door)
			{
				if($door->getHidden($actor->getRoom()->getId()) > 0)
					return Server::out($actor, 'Alas, you cannot go that way.');
				if($door->getDisposition() != Door::DISPOSITION_OPEN)
					return Server::out($actor, ucfirst($door->getShort()) . ' is ' . $door->getDisposition() . '.');
			}
			
			if($args[0] > 0)
			{
				if($actor->getMovement() >= $actor->getRace()->getMovementCost())
				{
					$actor->setMovement($actor->getMovement() - $actor->getRace()->getMovementCost());
					ActorObserver::instance()->updateRoomChange($actor, 'leaving ' . $args[1]);
					$actor->setRoom(Room::find($args[0]));
					if($actor instanceof \Living\User)
						Command::find('Look')->perform($actor);
					
					ActorObserver::instance()->updateRoomChange($actor, 'arriving');
					
					return;
				}
				Server::out($actor, 'You are too exhausted.');
			}
			else
				Server::out($actor, 'Alas, you cannot go that way.');
		
		}
	
	}

?>
