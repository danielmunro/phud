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
	use \Mechanics\Alias,
		\Mechanics\Server,
		\Items\Drink as iDrink,
		\Items\Food as iFood,
		\Living\User as lUser,
		\Mechanics\Item as mItem,
		\Mechanics\Equipment as mEquipment,
		\Mechanics\Command\DM,
		\Items\Weapon;

	class Item extends DM
	{
	
		protected function __construct()
		{
			self::addAlias('item', $this);
		}
	
		public function perform(lUser $user, $args = array())
		{
		
			if(sizeof($args) < 3)
				return Server::out($user, "What were you trying to do?");
		
			$item = $user->getInventory()->getItemByInput($args[1]);
			if(!$item) {
				$item = $user->getRoom()->getInventory()->getItemByInput($args[1]);
			}
			$value = implode(' ', array_slice($args, 3));
			
			$command = '';
			if($item instanceof Weapon)
				$command = $this->getWeaponCommand($args[2]);
			else if($item instanceof mEquipment)
				$command = $this->getEquipmentCommand($args[2]);
			else if($item instanceof iDrink)
				$command = $this->getDrinkCommand($args[2]);
			else if($item instanceof iFood)
				$command = $this->getFoodCommand($args[2]);
			else if($item instanceof mItem)
				$command = $this->getItemCommand($args[2]);

			if(!$item)
				return Server::out($user, "You can't find it.");
			
			if(!$command)
				return Server::out($user, "You can't do that.");
			
			$fn = 'do'.ucfirst($command);
			$this->$fn($user, $item, $value, $args);
		}
		
		private function doInformation(lUser $user, mItem $item, $value, $args)
		{
			Server::out($user, $item->getInformation());
		}
		
		private function doNouns(lUser $user, mItem $item, $value, $args)
		{
			$item->setNouns($value);
			return Server::out($user, $item->getShort()."'s nouns now set to: ".$item->getNouns());
		}
		
		private function doShort(lUser $user, mItem $item, $value, $args)
		{
			$old_short = ucfirst($item);
			$arg_short = implode(' ', array_slice($args, 3));
			$item->setShort($arg_short);
			Server::out($user, $old_short."'s short description is now set to: ".$item);
		}
		
		private function doLong(lUser $user, mItem $item, $value, $args)
		{
			$item->setLong($value);
			Server::out($user, ucfirst($item)."'s long description is now set to: ".$item->getLong());
		}
		
		private function doMaterial(lUser $user, mItem $item, $value, $args)
		{
			$material = mItem::findMaterial($value);
			if($material)
			{
				$item->setMaterial($material);
				return Server::out($user, ucfirst($item)."'s new material is: ".$item->getMaterial());
			}
			Server::out($user, "That material doesn't exist.");
		}
		
		private function doValue(lUser $user, mItem $item, $value, $args)
		{
			if(!is_numeric($value))
				return Server::out($user, "You can't set ".$item."'s value to that.");
			
			$item->setValue($value);
			Server::out($user, ucfirst($item)." is now worth ".$item->getValue()." copper.");
		}
		
		private function doWeight(lUser $user, mItem $item, $value, $args)
		{
			if(!is_numeric($value) || $value < 0 || $value > 1000)
				return Server::out($user, "You can't set ".$item."'s weight to that.");
			
			$item->setWeight($value);
			Server::out($user, ucfirst($item)." now weighs ".$item->getWeight()." pounds.");
		}
		
		private function doOwnable(lUser $user, mItem $item, $value, $args)
		{
			$item->setCanOwn($value);
			Server::out($user, ucfirst($item)." ".($item->getCanOwn()?"can":"cannot")." be owned.");
		}
		
		private function doLevel(lUser $user, mItem $item, $value, $args)
		{
			if(intval($value) == $value && $value > 0 && $value < 52)
			{
				$item->setLevel($value);
				return Server::out($user, ucfirst($item)." is now level ".$item->getLevel().".");
			}
			Server::out($user, "That is not a valid level.");
		}
		
		private function doType(lUser $user, mItem $item, $value, $args)
		{
			$item->setWeaponType($value);
			Server::out($user, ucfirst($item)." morphs into a ".$item->getWeaponTypeLabel().".");
		}
		
		private function doDamage(lUser $user, mItem $item, $value, $args)
		{
			$item->setDamageType($value);
			Server::out($user, ucfirst($item)." now does ".$item->getDamageType()." damage.");
		}
		
		private function doVerb(lUser $user, mItem $item, $value, $args)
		{
			$item->setVerb($value);
			Server::out($user, ucfirst($item)."'s verb is now: ".$item->getVerb().".");
		}
		
		private function doPosition(lUser $user, mItem $item, $position, $args)
		{
			$position = mEquipment::getPositionByStr($position);
			if($position !== false)
			{
				$item->setPosition($position);
				return Server::out($user, ucfirst($item)."'s position is now: ".$position.".");
			}
			Server::out($user, "That's an invalid position.");
		}

		private function doThirst(lUser $user, mItem $item, $thirst, $args)
		{
			$item->setThirst($thirst);
			Server::out($user, ucfirst($item)." will quench thirst for ".$thirst.".");
		}

		private function doAmount(lUser $user, mItem $item, $amount, $args)
		{
			$item->setAmount($amount);
			Server::out($user, ucfirst($item)." can be used up to ".$amount." times.");
		}

		private function doContents(lUser $user, mItem $item, $contents, $args)
		{
			$item->setContents($contents);
			Server::out($user, ucfirst($item)." contents are now: ".$contents.".");
		}

		private function doNourishment(lUser $user, mItem $item, $nourishment, $args)
		{
			$item->setNourishment($nourishment);
			Server::out($user, ucfirst($item)."'s nourishment is ".$nourishment.".");
		}
		
		private function getItemCommand($arg)
		{
			return $this->getCommand($arg, array('information', 'nouns', 'short', 'long', 'material', 'worth', 'value', 'weight', 'ownable', 'level'));
		}
		
		private function getEquipmentCommand($arg)
		{
			$command = $this->getItemCommand($arg);
			if($command)
				return $command;
			return $this->getCommand($arg, array('position'));
		}
		
		private function getWeaponCommand($arg)
		{
			$command = $this->getEquipmentCommand($arg);
			if($command)
				return $command;
			return $this->getCommand($arg, array('type', 'damage', 'verb'));
		}

		private function getDrinkCommand($arg)
		{
			$command = $this->getItemCommand($arg);
			if($command)
				return $command;
			return $this->getCommand($arg, ['thirst', 'amount', 'contents']);
		}
		
		private function getFoodCommand($arg)
		{
			$command = $this->getItemCommand($arg);
			if($command)
				return $command;
			return $this->getCommand($arg, ['nourishment']);
		}
		
		private function getCommand($arg, $commands)
		{
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
