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
	use \Mechanics\Server,
		\Mechanics\Alias,
		\Mechanics\Item as mItem,
		\Mechanics\Quest\Quest,
		\Mechanics\Command\DM,
		\Items\Armor,
		\Items\Weapon,
		\Items\Food,
		\Items\Drink as iDrink,
		\Items\Container,
		\Living\Mob,
		\Living\User as lUser,
		\Living\Shopkeeper as lShopkeeper,
		\Living\Questmaster;

	class Create extends DM
	{
	
		protected function __construct()
		{
			new Alias('create', $this);
		}
	
		public function perform(lUser $user, $args = array())
		{
			switch($args[1])
			{
				case strpos($args[1], 'mob') === 0:
					return $this->doCreateMob($user, $args);
				case strpos($args[1], 'shopkeeper') === 0:
					return $this->doCreateShopkeeper($user, $args);
				case strpos($args[1], 'quest') === 0:
					return $this->doCreateQuest($user, $args);
				case strpos($args[1], 'questmaster') === 0:
					return $this->doCreateQuestmaster($user, $args);
				case strpos($args[1], 'armor') === 0:
					return $this->doCreateItem($user, new Armor(), $args);
				case strpos($args[1], 'weapon') === 0:
					return $this->doCreateItem($user, new Weapon(), $args);
				case strpos($args[1], 'food') === 0:
					return $this->doCreateItem($user, new Food(), $args);
				case strpos($args[1], 'drink') === 0:
					return $this->doCreateItem($user, new iDrink(), $args);
				case strpos($args[1], 'container') === 0:
					return $this->doCreateContainer($user, new Container(), $args);
				case isset($args[2]) && strpos($args[2], 'copper') === 0:
					return $this->doCreateCopper($user, $args[1]);
				default:
					return Server::out($user, "What do you want to create?");
			}
		}
		
		private function doCreateCopper($user, $amount)
		{
			$user->addCopper($amount);
			Server::out($user, "You create ".$amount." copper.");
		}
		
		private function doCreateItem(lUser $user, mItem $item, $args)
		{
			if(sizeof($args) > 2)
			{
				$short = implode(' ', array_slice($args, 2));
				$item->setShort($short);
			}
			$user->getInventory()->add($item);
			return Server::out($user, ucfirst($item->getShort())." poofs into existence.");
		}
		
		private function doCreateMob(lUser $user, $args)
		{
			$mob = new Mob();
			$mob->setRoom($user->getRoom());
			$mob->setStartRoom();
			$mob->save();
			
			$mob->getRoom()->announce($mob, $mob->getAlias(true)." poofs into existence.");
		}
		
		private function doCreateShopkeeper(lUser $user, $args)
		{
			$shopkeeper = new lShopkeeper();
			$shopkeeper->setRoom($user->getRoom());
			$shopkeeper->save();
			
			$shopkeeper->getRoom()->announce($shopkeeper, $shopkeeper->getAlias(true)." poofs into existence.");
		}
		
		private function doCreateQuestmaster(lUser $user, $args)
		{
			$questmaster = new Questmaster();
			$questmaster->setRoom($user->getRoom());
			$questmaster->save();
			
			$questmaster->getRoom()->announce($questmaster, $questmaster->getAlias(true)." poofs into existence.");
		}

		private function doCreateQuest(lUser $user, $args)
		{
			$user->getQuestLog()->add(new QuestInstance($user, new Quest()));
			Server::out($user, "You've obtained a new quest!");
		}
	}
?>
