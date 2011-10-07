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
	use \Mechanics\Item as mItem;
	use \Mechanics\Quest\Quest;
	use \Items\Armor;
	use \Items\Weapon;
	use \Items\Food;
	use \Items\Drink as iDrink;
	use \Items\Container;
	use \Living\Mob;
	use \Living\Shopkeeper as lShopkeeper;
	use \Living\Questmaster;
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
				case strpos($args[1], 'quest') === 0:
					return $this->doCreateQuest($actor, $args);
				case strpos($args[1], 'questmaster') === 0:
					return $this->doCreateQuestmaster($actor, $args);
				case strpos($args[1], 'armor') === 0:
					return $this->doCreateItem($actor, new Armor(), $args);
				case strpos($args[1], 'weapon') === 0:
					return $this->doCreateItem($actor, new Weapon(), $args);
				case strpos($args[1], 'food') === 0:
					return $this->doCreateItem($actor, new Food(), $args);
				case strpos($args[1], 'drink') === 0:
					return $this->doCreateItem($actor, new iDrink(), $args);
				case strpos($args[1], 'container') === 0:
					return $this->doCreateContainer($actor, new Container(), $args);
				case strpos($args[2], 'copper') === 0:
					return $this->doCreateCopper($actor, $args[1]);
				default:
					return Server::out($actor, "What do you want to create?");
			}
		}
		
		private function doCreateCopper($actor, $amount)
		{
			$actor->addCopper($amount);
			Server::out($actor, "You create ".$amount." copper.");
		}
		
		private function doCreateItem(Actor $actor, mItem $item, $args)
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
			$mob = new Mob();
			$mob->setRoom($actor->getRoom());
			$mob->setStartRoom();
			$mob->save();
			
			$mob->getRoom()->announce($mob, $mob->getAlias(true)." poofs into existence.");
		}
		
		private function doCreateShopkeeper(Actor $actor, $args)
		{
			$shopkeeper = new lShopkeeper();
			$shopkeeper->setRoom($actor->getRoom());
			$shopkeeper->save();
			
			$shopkeeper->getRoom()->announce($shopkeeper, $shopkeeper->getAlias(true)." poofs into existence.");
		}
		
		private function doCreateQuestmaster(Actor $actor, $args)
		{
			$questmaster = new Questmaster();
			$questmaster->setRoom($actor->getRoom());
			$questmaster->save();
			
			$questmaster->getRoom()->announce($questmaster, $questmaster->getAlias(true)." poofs into existence.");
		}

		private function doCreateQuest(Actor $actor, $args)
		{
			$actor->getQuestLog()->add(new QuestInstance($actor, new Quest()));
			Server::out($actor, "You've obtained a new quest!");
		}
	}
?>
