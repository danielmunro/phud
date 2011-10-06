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
	class AttSet extends \Mechanics\Command implements \Mechanics\Command_DM
	{
		
		protected function __construct()
		{
			new Alias('attset', $this);
		}
		
		public function perform(Actor $actor, $args = array())
		{
			$object = $actor->getRoom()->getActorByInput($args[1]);
			if(!$object)
				$object = $actor->getRoom()->getInventory()->getItemByInput($args[1]);
			if(!$object)
				$object = $actor->getInventory()->getItemByInput($args[1]);
			if(!$object)
				return Server::out($actor, "That doesn't seem to exist.");
		
			$label = '';
			if($object instanceof Actor)
				$label = $object->getAlias();
			else if($object instanceof mItem)
				$label = $object->getShort();
		
			$atts = $object->getAttributes();
		
			if(strpos('hp', $args[2]) === 0)
			{
				$atts->setHp($args[3]);
				$atts->setMaxHp($args[3]);
				Server::out($actor, "You set ".$label."'s hp to ".$args[3].".");
			}
			else if(strpos('mana', $args[2]) === 0)
			{
				$atts->setMana($args[3]);
				$atts->setMaxMana($args[3]);
				Server::out($actor, "You set ".$label."'s mana to ".$args[3].".");
			}
			else if(strpos('movement', $args[2]) === 0)
			{
				$atts->setMovement($args[3]);
				$atts->setMaxMovement($args[3]);
				Server::out($actor, "You set ".$label."'s movement to ".$args[3].".");
			}
			else if(strpos('str', $args[2]) === 0)
			{
				$atts->setStr($args[3]);
				Server::out($actor, "You set ".$label."'s str to ".$args[3].".");
			}
			else if(strpos('int', $args[2]) === 0)
			{
				$atts->setInt($args[3]);
				Server::out($actor, "You set ".$label."'s int to ".$args[3].".");
			}
			else if(strpos('wis', $args[2]) === 0)
			{
				$atts->setWis($args[3]);
				Server::out($actor, "You set ".$label."'s wis to ".$args[3].".");
			}
			else if(strpos('dex', $args[2]) === 0)
			{
				$atts->setDex($args[3]);
				Server::out($actor, "You set ".$label."'s dex to ".$args[3].".");
			}
			else if(strpos('con', $args[2]) === 0)
			{
				$atts->setCon($args[3]);
				Server::out($actor, "You set ".$label."'s con to ".$args[3].".");
			}
			else if(strpos('cha', $args[2]) === 0)
			{
				$atts->setCha($args[3]);
				Server::out($actor, "You set ".$label."'s cha to ".$args[3].".");
			}
			else if(strpos('ac_bash', $args[2]) === 0)
			{
				$atts->setAcBash($args[3]);
				Server::out($actor, "You set ".$label."'s bash ac to ".$args[3].".");
			}
			else if(strpos('ac_slash', $args[2]) === 0)
			{
				$atts->setAcSlash($args[3]);
				Server::out($actor, "You set ".$label."'s slash ac to ".$args[3].".");
			}
			else if(strpos('ac_pierce', $args[2]) === 0)
			{
				$atts->setStr($args[3]);
				Server::out($actor, "You set ".$label."'s pierce ac to ".$args[3].".");
			}
			else if(strpos('ac_magic', $args[2]) === 0)
			{
				$atts->setStr($args[3]);
				Server::out($actor, "You set ".$label."'s magic ac to ".$args[3].".");
			}
			else if(strpos('hit', $args[2]) === 0)
			{
				$atts->setHit($args[3]);
				Server::out($actor, "You set ".$label."'s hit roll to ".$args[3].".");
			}
			else if(strpos('dam', $args[2]) === 0)
			{
				$atts->setDam($args[3]);
				Server::out($actor, "You set ".$label."'s dam roll to ".$args[3].".");
			}
			else if(strpos('saves', $args[2]) === 0)
			{
				$atts->setSaves($args[3]);
				Server::out($actor, "You set ".$label."'s saves to ".$args[3].".");
			}
			if($object instanceof Actor && method_exists($object, 'save'))
				$object->save();
		}
	}
?>
