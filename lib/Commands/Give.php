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
		\Mechanics\Actor,
		\Mechanics\Command\Command,
		\Living\Shopkeeper as lShopkeeper,
		\Living\User as lUser;
	
	class Give extends Command
	{
	
		protected $dispositions = array(Actor::DISPOSITION_STANDING, Actor::DISPOSITION_SITTING);
	
		protected function __construct()
		{
			self::addAlias('give', $this);
		}
	
		public function perform(Actor $actor, $args = array())
		{
		
			if(
				is_string($args[0]) &&
				strcmp(((int) $args[1]), $args[1]) === 0 &&
				is_string($args[2]))
			{
				$give_amount = $args[1];
				$currency = $args[2];
				$actors = $actor->getRoom()->getActors();
				$target = $actor->getRoom()->getActorByInput($args);
				
				if(!($target instanceof Actor))
					return Server::out($actor, "They aren't here.");
				
				if(strpos('copper', $currency) === 0)
					$currency_proper = 'copper';
				else if(strpos('silver', $currency) === 0)
					$currency_proper = 'silver';
				else if(strpos('gold', $currency) === 0)
					$currency_proper = 'gold';
				
				if(!isset($currency_proper))
					return Server::out($actor, "What kind of currency?");
				
				$amount = $actor->{'get' . ucfirst($currency_proper)}();
				
				if($amount < $give_amount)
					return Server::out($actor, "You don't have that.");
				
				$fn = 'add'.ucfirst($currency_proper);
				$actor->$fn(-$give_amount);
				$target->$fn($give_amount);
				
				Server::out($actor, "You give " . $give_amount . " " . $currency_proper . " to ".$target.".");
				Server::out($target, $actor->getAlias(true) . " gives you " . $give_amount . " " . $currency_proper . ".");
			}
			else
			{
				$item = $actor->getInventory()->getItemByInput($args);
				$target = $actor->getRoom()->getActorByInput($args);
			
				if(empty($item))
					return Server::out($actor, "You don't appear to have that.");
			
				if($target instanceof lShopkeeper && $actor instanceof lUser && !$actor->isDM())
					return Server::out($actor, ucfirst($target)." doesn't look interested.");
			
				if(!($target instanceof Actor))
					return Server::out($actor, "You don't see them here.");
				
				$actor->getInventory()->remove($item);
				$target->getInventory()->add($item);
				Server::out($actor, "You give ".$item." to ".$target.".");
				Server::out($target, ucfirst($actor)." gives you ".$item.".");
			}
		}
	
	}

?>
