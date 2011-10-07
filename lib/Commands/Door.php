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
	class Door extends \Mechanics\Command implements \Mechanics\Command_DM
	{
	
		protected function __construct()
		{
			new \Mechanics\Alias('door', $this);
		}
	
		public function perform(\Mechanics\Actor $actor, $args = array())
		{
			if(sizeof($args) <= 1)
				return \Mechanics\Server::out($actor, "What were you trying to do?");
		
			$command = $this->getCommand($args[1]);
			if($command)
			{
				$fn = 'do'.ucfirst($command);
				$this->$fn($actor, $args);
			}
		}
		
		private function doInformation(\Mechanics\Actor $actor, $args)
		{
		}
		
		private function doCreate(\Mechanics\Actor $actor, $args)
		{
			if(!$this->hasArgCount($actor, $args, 3))
				return;
			
			$direction = \Mechanics\Room::getDirectionStr($args[2]);
			if(!$direction)
				return \Mechanics\Server::out($actor, "That direction doesn't exist.");
			
			$door1 = new \Mechanics\Door();
			
			$fn = 'get'.ucfirst($direction);
			$dir_id = $actor->getRoom()->$fn();
			$room = \Mechanics\Room::find($dir_id);
			$rev_dir = \Mechanics\Room::getReverseDirection($direction);
			$door2 = new \Mechanics\Door();
			$door1->setPartnerDoor($door2);
			$door2->setPartnerDoor($door1);
			$actor->getRoom()->setDoor($direction, $door1);
			$room->setDoor($rev_dir, $door2);
			
			\Mechanics\Server::out($actor, "You have created ".$door1->getShort()." to the ".$direction." direction, and ".$door2->getShort()." to the ".$rev_dir." direction.");
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
