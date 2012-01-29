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
	use \Mechanics\Alias;
	use \Mechanics\Server;
	use \Mechanics\Door as mDoor;
	use \Mechanics\Room as mRoom;
	use \Mechanics\Command\DM;
	use \Living\User;

	class Door extends DM
	{
	
		protected function __construct()
		{
			self::addAlias('door', $this);
		}
	
		public function perform(User $user, $args = array())
		{
			if(sizeof($args) <= 1)
				return Server::out($user, "What were you trying to do?");
		
			$command = $this->getCommand($args[1]);
			if($command)
			{
				$fn = 'do'.ucfirst($command);
				$this->$fn($user, $args);
			}
		}
		
		private function doInformation(User $user, $args)
		{
		}
		
		private function doCreate(User $user, $args)
		{
			if(!$this->hasArgCount($user, $args, 3))
				return;
			
			$direction = mRoom::getDirectionStr($args[2]);
			if(!$direction)
				return Server::out($user, "That direction doesn't exist.");
			
			$door1 = new mDoor();
			
			$fn = 'get'.ucfirst($direction);
			$dir_id = $user->getRoom()->$fn();
			$room = mRoom::find($dir_id);
			$rev_dir = mRoom::getReverseDirection($direction);
			$door2 = new mDoor();
			$door1->setPartnerDoor($door2);
			$door2->setPartnerDoor($door1);
			$user->getRoom()->setDoor($direction, $door1);
			$room->setDoor($rev_dir, $door2);
			
			Server::out($user, "You have created ".$door1." to the ".$direction." direction, and ".$door2." to the ".$rev_dir." direction.");
		}
		
		private function getCommand($arg)
		{
			$commands = array('information', 'create');
			
			$command = array_filter($commands, function($c) use ($arg) 
				{
					return strpos($c, $arg) === 0;
				});
			
			if(sizeof($command))
				return str_replace(' ', '', array_shift($command));
			
			return false;
		}
	}
?>
