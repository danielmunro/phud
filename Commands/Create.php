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
	class Create extends \Mechanics\Command
	{
	
		protected function __construct()
		{
			new \Mechanics\Alias('create', $this);
		}
	
		public function perform(\Mechanics\Actor $actor, $args = array())
		{
			switch($args[1])
			{
				case strpos($args[1], 'mob') === 0:
					return $this->doCreateMob($actor, $args);
				case strpos($args[1], 'armor') === 0:
					return $this->doCreateItem($actor, new \Items\Armor(), $args);
				case strpos($args[1], 'weapon') === 0:
					return $this->doCreateItem($actor, new \Items\Weapon(), $args);
				case strpos($args[1], 'food') === 0:
					return $this->doCreateItem($actor, new \Items\Food(), $args);
				case strpos($args[1], 'drink') === 0:
					return $this->doCreateItem($actor, new \Items\Drink(), $args);
				case strpos($args[1], 'container') === 0:
					return $this->doCreateContainer($actor, new \Items\Container(), $args);
				default:
					return \Mechanics\Server::out($actor, "What do you want to create?");
			}
		}
		
		private function doCreateItem(\Mechanics\Actor $actor, \Mechanics\Item $item, $args)
		{
			if(sizeof($args) > 2)
			{
				$short = implode(' ', array_slice($args, 2));
				$item->setShort($short);
			}
			$actor->getInventory()->add($item);
			return \Mechanics\Server::out($actor, ucfirst($item->getShort())." poofs into existence.");
		}
		
		private function doCreateMob(\Mechanics\Actor $actor, $args)
		{
			if(sizeof($args) <= 2)
				return \Mechanics\Server::out($actor, "You need to specify a name for your new mob.");
			
			$alias = implode(' ', array_slice($args, 2));
			$nouns = substr($alias, strrpos($alias, ',')+1);
			$alias = substr($alias, 0, strrpos($alias, ','));
			
			if(!\Living\Mob::validateAlias($alias))
				return \Mechanics\Server::out($actor, "\"".$alias."\" is not a valid name for a mob.");
			
			$mob = new \Living\Mob();
			$mob->setAlias($alias);
			$mob->setNouns($nouns);
			$mob->setRoom($actor->getRoom());
			$mob->setStartRoom();
			$mob->save();
			
			$mob->getRoom()->announce($mob, $mob->getAlias(true)." poofs into existence.");
		}
	}
?>
