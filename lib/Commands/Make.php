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
		\Mechanics\Command\DM,
		\Mechanics\Command\Command,
		\Living\User as lUser;

	class Make extends DM
	{
	
		protected function __construct()
		{
			self::addAlias('make', $this);
		}
	
		public function perform(lUser $user, $args = array())
		{
			$target = $user->getRoom()->getActorByInput($args[1]);
			$command = Command::lookup($args[2]);
			if($target && $command)
			{
				$command['lookup']->perform($target, $args);
				Server::out($user, "Done.");
			}
			else
			{
				Server::out($user, "Cannot be done.");
			}
		}
	
	}
?>
