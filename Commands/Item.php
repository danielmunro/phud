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
	use \Mechanics\Alias;
	use \Mechanics\Server;
	use \Mechanics\Actor;
	use \Mechanics\Item as mItem;
	class Item extends \Mechanics\Command
	{
	
		protected function __construct()
		{
			new Alias('item', $this);
		}
	
		public function perform(Actor $actor, $args = array())
		{
		
			if(sizeof($args) < 3)
				return Server::out($actor, "What were you trying to do?");
		
			$item = $actor->getInventory()->getItemByInput($args[1]);
			$value = implode(' ', array_slice($args, 3));
			
			$command = '';
			switch(get_class($item))
			{
				case 'Items\Weapon':
					$command = $this->getWeaponCommand($args[2]);
					break;
				case 'Mechanics\Item':
					$command = $this->getItemCommand($args[2]);
					break;
			}
			
			if(!$item)
				return Server::out($actor, "You can't find it.");
			
			if(!$command)
				return Server::out($actor, "You can't do that.");
			
			$fn = 'do'.ucfirst($command);
			$this->$fn($actor, $item, $value, $args);
		}
		
		private function doInformation(Actor $actor, mItem $item, $value, $args)
		{
			Server::out($actor, $item->getInformation());
		}
		
		private function doNouns(Actor $actor, mItem $item, $value, $args)
		{
			$item->setNouns($value);
			return Server::out($actor, $item->getShort()."'s nouns now set to: ".$item->getNouns());
		}
		
		private function doShort(Actor $actor, mItem $item, $value, $args)
		{
			$old_short = $item->getShort(true);
			$arg_short = implode(' ', array_slice($args, 3));
			$item->setShort($arg_short);
			Server::out($actor, $old_short."'s short description is now set to: ".$item->getShort());
		}
		
		private function doLong(Actor $actor, mItem $item, $value, $args)
		{
			$item->setLong($value);
			Server::out($actor, $item->getShort(true)."'s long description is now set to: ".$item->getLong());
		}
		
		private function doMaterial(Actor $actor, mItem $item, $value, $args)
		{
			$material = \Mechanics\Item::findMaterial($value);
			if($material)
			{
				$item->setMaterial($material);
				return Server::out($actor, $item->getShort(true)."'s new material is: ".$item->getMaterial());
			}
			Server::out($actor, "That material doesn't exist.");
		}
		
		private function doValue(Actor $actor, mItem $item, $value, $args)
		{
			if(!is_numeric($value))
				return Server::out($actor, "You can't set ".$item->getShort()."'s value to that.");
			
			$item->setValue($value);
			Server::out($actor, $item->getShort(true)." is now worth ".$item->getValue()." copper.");
		}
		
		private function doWeight(Actor $actor, mItem $item, $value, $args)
		{
			if(!is_numeric($value) || $value < 0 || $value > 1000)
				return Server::out($actor, "You can't set ".$item->getShort()."'s weight to that.");
			
			$item->setWeight($value);
			Server::out($actor, $item->getShort(true)." now weighs ".$item->getWeight()." pounds.");
		}
		
		private function doOwnable(Actor $actor, mItem $item, $value, $args)
		{
			$item->setCanOwn($value);
			Server::out($actor, $item->getShort(true)." ".($item->getCanOwn()?"can":"cannot")." be owned.");
		}
		
		private function doLevel(Actor $actor, mItem $item, $value, $args)
		{
			if(intval($value) == $value && $value > 0 && $value < 52)
			{
				$item->setLevel($value);
				return Server::out($actor, $item->getShort(true)." is now level ".$item->getLevel().".");
			}
			Server::out($actor, "That is not a valid level.");
		}
		
		private function doType(Actor $actor, mItem $item, $value, $args)
		{
			$item->setWeaponType($value);
			Server::out($actor, $item->getShort(true)." morphs into a ".$item->getWeaponTypeLabel().".");
		}
		
		private function doDamage(Actor $actor, mItem $item, $value, $args)
		{
			$item->setDamageType($value);
			Server::out($actor, $item->getShort(true)." now does ".\Mechanics\Damage::getDamageTypeLabel($item->getDamageType())." damage.");
		}
		
		private function doVerb(Actor $actor, mItem $item, $value, $args)
		{
			$item->setVerb($value);
			Server::out($actor, $item->getShort(true)."'s verb is now: ".$item->getVerb().".");
		}
		
		private function getItemCommand($arg)
		{
			return $this->getCommand($arg, array('information', 'nouns', 'short', 'long', 'material', 'worth', 'value', 'weight', 'ownable', 'level'));
		}
		
		private function getWeaponCommand($arg)
		{
			return $this->getCommand($arg, array('information', 'nouns', 'short', 'long', 'material', 'worth', 'value', 'weight', 'ownable', 'level', 'type', 'damage', 'verb'));
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
