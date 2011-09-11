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
	use \Mechanics\Item;
	use \Mechanics\Alias;
	use \Mechanics\Actor;
	use \Mechanics\Server;
	use \Mechanics\Ability;
	use \Living\Shopkeeper;
	class Buy extends \Mechanics\Command
	{
	
		protected $dispositions = array(Actor::DISPOSITION_STANDING, Actor::DISPOSITION_SITTING);
	
		protected function __construct()
		{
			new Alias('buy', $this);
		}
	
		public function perform(Actor $actor, $args = array())
		{
		
			if(sizeof($args) == 3)
				$target = $actor->getRoom()->getActorByInput();
			else
			{
				$targets = $actor->getRoom()->getActors();
				foreach($targets as $potential_target)
					if($potential_target instanceof Shopkeeper)
						$target = $potential_target;
			}
			
			if(!($target instanceof Actor))
				return Server::out($actor, "They are not here.");
			
			if(!($target instanceof Shopkeeper))
				return Server::out($actor, $target->getAlias(true) . " is not a shop keeper.");
			
			$item = $target->getInventory()->getItemByInput($args[1]);
			
			if(!($item instanceof Item))
				return Say::perform($target, $target->getNoItemMessage());
			
			$value = $item->getValue();
			$abilities = $actor->getAbilitySet()->getAbilitiesByHook(Ability::HOOK_BUY_ITEM);
			foreach($abilities as $learned_ability)
				$value += $learned_ability->perform($value);
		
			if($actor->decreaseFunds($value) === false)
				return Say::perform($target, $target->getNotEnoughMoneyMessage());
			
			$new_item = clone $item;
			$actor->getInventory()->add($new_item);
			return Server::out($actor, "You buy " . $item->getShort() . " for " . $item->getValue() . " copper.");
		}
	}
?>
