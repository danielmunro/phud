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
	class Get extends \Mechanics\Command
	{
	
		protected static $dispositions = array(\Mechanics\Actor::DISPOSITION_STANDING, \Mechanics\Actor::DISPOSITION_SITTING);
	
		protected function __construct()
		{
		
			\Mechanics\Command::addAlias(__CLASS__, 'get');
		}
	
		public static function perform(&$actor, $args = null)
		{
		
			if(sizeof($args) == 2)
			{
				$item = $actor->getRoom()->getInventory()->getItemByInput($args);
				$container = $actor->getRoom();
			}
			else
			{
				
				array_shift($args);
				
				// getting something from somewhere
				$container = $actor->getRoom()->getInventory()->getContainerByInput($args);
				if(!($container instanceof \Items\Container))
					$container = $actor->getInventory()->getContainerByInput($args);
				if(!($container instanceof \Items\Container))
					return \Mechanics\Server::out($actor, "Nothing is there.");
				
				if($args[0] == 'all')
				{
					foreach($container->getInventory()->getItems() as $item)
					{
						$container->getInventory()->remove($item);
						$actor->getInventory()->add($item);
						\Mechanics\Server::out($actor, 'You get ' . $item->getShort() . ' from ' . $container->getShort() . '.');
					}
					return;
				}
				else
				{
				
					$item = $container->getInventory()->getItemByInput(array('', $args[0]));
				
					if($item instanceof \Items\Item)
						$from = ' from ' . $container->getShort();
					else
						return \Mechanics\Server::out($actor, "You see nothing like that.");
				}
			}
			
			if($item instanceof \Items\Item)
			{
				if(!$item->getCanOwn())
					return \Mechanics\Server::out($actor, "You cannot pick that up.");
				
				$container->getInventory()->remove($item);
				$actor->getInventory()->add($item);
				\Mechanics\Server::out($actor, 'You get ' . $item->getShort() . (isset($from) ? $from : '') . '.');
			}
			else
			{
				\Mechanics\Server::out($actor, 'You see nothing like that.');
			}
		
		}
	
	}

?>
