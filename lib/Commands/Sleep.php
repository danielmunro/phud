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
		\Mechanics\Actor,
		\Mechanics\Command\Command;
		
	class Sleep extends Command
	{
		protected $dispositions = [Actor::DISPOSITION_SLEEPING];
		protected function __construct()
		{
			self::addAlias('sleep', $this);
		}
	
		public function perform(Actor $actor, $args = array())
		{
			if($actor->getDisposition() === Actor::DISPOSITION_SLEEPING)
				return Server::out($actor, "You are already sleeping.");
			
			Server::out($actor, "You lie down and go to sleep.");
			$actor->getRoom()->announce($actor, ucfirst($actor)." lies down and goes to sleep.");
			$actor->setDisposition(Actor::DISPOSITION_SLEEPING);
		}
	
	}
?>
