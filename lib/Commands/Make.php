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
	class Make extends \Mechanics\Command implements \Mechanics\Command_DM
	{
	
		protected function __construct()
		{
			new \Mechanics\Alias('make', $this);
		}
	
		public function perform(\Mechanics\Actor $actor, $args = array())
		{
			$target = $actor->getRoom()->getActorByInput($args[1]);
			$command = \Mechanics\Alias::lookup($args[2]);
			if($target && $command)
			{
				$command->perform($target, $args);
				\Mechanics\Server::out($actor, "Done.");
			}
			else
			{
				\Mechanics\Server::out($actor, "Cannot be done.");
			}
		}
	
	}
?>
