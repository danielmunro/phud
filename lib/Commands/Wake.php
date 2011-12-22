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
		\Mechanics\Actor,
		\Mechanics\Server,
		\Mechanics\Command\Command;

	class Wake extends Command
	{
		protected function __construct()
		{
			self::addAlias('wake', $this);
		}
	
		public function perform(Actor $actor, $args = array())
		{
			if($actor->getDisposition() === Actor::DISPOSITION_STANDING)
				return Server::out($actor, "You are already awake.");
			
			if(array_key_exists('sleep', $actor->getAffects()))
				return Server::out($actor, "You can't wake up!");
			
			Server::out($actor, "You wake up and stand up.");
			$actor->getRoom()->announce($actor, ucfirst($actor)." wakes up and stands up.");
			$actor->setDisposition(Actor::DISPOSITION_STANDING);
		}
	
	}
?>
