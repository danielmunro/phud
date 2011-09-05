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
	use \Mechanics\Server;
	class Item extends \Mechanics\Command
	{
	
		protected function __construct()
		{
			new \Mechanics\Alias('item', $this);
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
			if(!$this->hasArgCount($actor, $args, 3))
				return;
			
			$item = $actor->getInventory()->getItemByInput($args[2]);
			if(!$item)
				$item = $actor->getRoom()->getInventory()->getItemByInput($args[2]);
			
			if($item instanceof \Mechanics\Item)
				Server::out($actor, $item->getInformation());
		}
		
		private function getCommand($arg)
		{
			$commands = array('information');
			
			$command = array_filter($commands, function($c) use ($arg) 
				{
					return strpos($c, $arg) === 0;
				});
			
			if(sizeof($command))
				return array_shift($command);
			
			return false;
		}
	}
?>
