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
		\Mechanics\Server,
		\Mechanics\Debug,
		\Mechanics\Alias,
		\Items\Item as iItem,
		\Items\Drink as iDrink,
		\Mechanics\Command\Command;

	class Drink extends Command
	{
	
		protected $dispositions = array(Actor::DISPOSITION_STANDING, Actor::DISPOSITION_SITTING);
	
		protected function __construct()
		{
			new Alias('drink', $this);
		}
		
		public function perform(Actor $actor, $args = array())
		{
			
			$item = null;
			/**
			@TODO redo this crap
			if(sizeof($args) > 1)
				$item = $actor->getInventory()->getItemByInput($args);
			
			if(!($item instanceof iItem))
			{
				$items = $actor->getRoom()->getInventory()->getItems();
				foreach($items as $i)
					if($i instanceof iDrink)
						$item = $i;
			}
			*/
			
			if(!($item instanceof iItem))
				return Server::out($actor, "Nothing like that is here.");
			
			if(!($item instanceof iDrink))
				return Server::out($actor, "You can't drink that!");
			
			if($actor->getNourishment() + $actor->getThirst() > $actor->getRace()->getFull())
				return Server::out($actor, "You are too full.");
			
			$actor->increaseThirst($item->getThirst());
			
			Server::out($actor, "You drink water from ".$item.".");
		}
	}
?>
