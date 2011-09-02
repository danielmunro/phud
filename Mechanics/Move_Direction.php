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

		protected $dispositions = array(Actor::DISPOSITION_STANDING);

		public function perform(Actor $actor, $args = array())
		{
		
			if($actor->getDisposition() === Actor::DISPOSITION_SITTING)
				return Server::out($actor, "You need to stand up to do that.");
			if($actor->getDisposition() === Actor::DISPOSITION_SLEEPING)
				return Server::out($actor, "You can't do anything, you're sleeping!");
		
			if($actor->getTarget())
				return Server::out($actor, 'You cannot leave a fight!');
			
			if($args[0] > -1)
			{
				$room = Room::find($args[0]);
				$door = $room->getDoor($args[1]);
				if($door instanceof Door)
				{
					if($door->isHidden())
						return Server::out($actor, 'Alas, you cannot go that way.');
					if($door->getDisposition() != Door::DISPOSITION_OPEN)
						return Server::out($actor, ucfirst($door->getShort()) . ' is ' . $door->getDisposition() . '.');
				}
				if($actor->getMovement() >= $actor->getRace()->getMovementCost() || $actor->getLevel() > Actor::MAX_LEVEL)
				{
					if($actor->getLevel() < Actor::MAX_LEVEL)
						$actor->setMovement($actor->getMovement() - $actor->getRace()->getMovementCost());
					$actor->getRoom()->announce($actor, $actor->getAlias(true) . ' ' . $actor->getRace()->getMoveVerb() . ' ' . $args[1] . '.');
					$actor->setRoom($room);
					if($actor instanceof \Living\User)
					{
						$look = Alias::lookup('look');
						$look->perform($actor);
					}
					$actor->getRoom()->announce($actor, $actor->getAlias(true) . ' has arrived.');
					
					return;
				}
				Server::out($actor, 'You are too exhausted.');
			}
			else
				Server::out($actor, 'Alas, you cannot go that way.');
		
		}
	
	}

?>
