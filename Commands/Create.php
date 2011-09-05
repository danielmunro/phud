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
	use \Mechanics\Alias;
	use \Mechanics\Actor;
	use \Mechanics\Item;
	use \Items\Armor;
	use \Items\Weapon;
	use \Items\Food;
	use \Items\Drink;
	use \Items\Container;
	class Create extends \Mechanics\Command implements \Mechanics\Command_DM
	{
	
		protected function __construct()
		{
			new Alias('create', $this);
		}
	
		public function perform(Actor $actor, $args = array())
		{
			switch($args[1])
			{
				case strpos($args[1], 'mob') === 0:
					return $this->doCreateMob($actor, $args);
				case strpos($args[1], 'shopkeeper') === 0:
					return $this->doCreateShopkeeper($actor, $args);
				case strpos($args[1], 'armor') === 0:
					return $this->doCreateItem($actor, new Armor(), $args);
				case strpos($args[1], 'weapon') === 0:
					return $this->doCreateItem($actor, new Weapon(), $args);
				case strpos($args[1], 'food') === 0:
					return $this->doCreateItem($actor, new Food(), $args);
				case strpos($args[1], 'drink') === 0:
					return $this->doCreateItem($actor, new Drink(), $args);
				case strpos($args[1], 'container') === 0:
					return $this->doCreateContainer($actor, new Container(), $args);
				default:
					return Server::out($actor, "What do you want to create?");
			}
		}
		
		private function doCreateItem(Actor $actor, Item $item, $args)
		{
			if(sizeof($args) > 2)
			{
				$short = implode(' ', array_slice($args, 2));
				$item->setShort($short);
			}
			$actor->getInventory()->add($item);
			return Server::out($actor, ucfirst($item->getShort())." poofs into existence.");
		}
		
		private function doCreateMob(Actor $actor, $args)
		{
			if(sizeof($args) <= 2)
				return Server::out($actor, "You need to specify a name for your new mob.");
			
			$alias = implode(' ', array_slice($args, 2));
			$nouns = substr($alias, strrpos($alias, ',')+1);
			$alias = substr($alias, 0, strrpos($alias, ','));
			
			if(!\Living\Mob::validateAlias($alias))
				return Server::out($actor, "\"".$alias."\" is not a valid name for a mob.");
			
			$mob = new \Living\Mob();
			$mob->setAlias($alias);
			$mob->setNouns($nouns);
			$mob->setRoom($actor->getRoom());
			$mob->setStartRoom();
			$mob->save();
			
			$mob->getRoom()->announce($mob, $mob->getAlias(true)." poofs into existence.");
		}
		
		private function doCreateShopkeeper(Actor $actor, $args)
		{
			$shopkeeper = new \Living\Shopkeeper();
			$shopkeeper->setRoom($actor->getRoom());
			$shopkeeper->save();
			
			$shopkeeper->getRoom()->announce($shopkeeper, $shopkeeper->getAlias(true)." poofs into existence.");
		}
	}
?>
