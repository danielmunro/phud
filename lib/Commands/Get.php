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
	use \Mechanics\Actor,
		\Mechanics\Alias,
		\Mechanics\Server,
		\Mechanics\Command\Command,
		\Items\Container,
		\Items\Item as iItem,
		\Mechanics\Item as mItem;

	class Get extends Command
	{
		protected $dispositions = array(Actor::DISPOSITION_STANDING, Actor::DISPOSITION_SITTING);
	
		protected function __construct()
		{
			self::addAlias('get', $this);
		}
	
		public function perform(Actor $actor, $args = array())
		{
		
			if(sizeof($args) === 2)
			{
				$item = $actor->getRoom()->getItemByInput($args[1]);
				$container = $actor->getRoom();
			}
			else
			{
				
				array_shift($args);
				
				// getting something from somewhere
				$container = $actor->getRoom()->getContainerByInput($args);
				if(!($container instanceof Container))
					$container = $actor->getContainerByInput($args);
				if(!($container instanceof Container))
					return Server::out($actor, "Nothing is there.");
				
				if($args[0] == 'all')
				{
					foreach($container->getItems() as $item)
					{
						$item->transferOwnership($container, $actor);
						Server::out($actor, 'You get '.$item.' from '.$container.'.');
					}
					return;
				}
				else
				{
				
					$item = $container->getItemByInput(array('', $args[0]));
				
					if($item instanceof iItem)
						$from = ' from ' . $container;
					else
						return Server::out($actor, "You see nothing like that.");
				}
			}
			
			if($item instanceof mItem)
			{
				if(!$item->getCanOwn())
					return Server::out($actor, "You cannot pick that up.");
				
				$container->removeItem($item);
				$actor->addItem($item);
				Server::out($actor, 'You get '.$item.(isset($from) ? $from : '') . '.');
			}
			else
			{
				Server::out($actor, 'You see nothing like that.');
			}
		
		}
	
	}

?>
