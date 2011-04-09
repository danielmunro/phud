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
	class Cast extends \Mechanics\Command
	{
		
		protected function __construct()
		{
			\Mechanics\Command::addAlias(__CLASS__, array('c', 'cast'));
		}
		
		public static function perform(&$actor, $args = null)
		{
			
			// DETERMINE THE SPELL
			// Get rid of 'cast'
			array_shift($args);
			$input = implode(' ', $args);
			
			$spell = $actor->getAbilitySet()->isValidSpell($input);
			if(!$spell)
			{
				$last = array_pop($args);
				$input = implode(' ', $args);
				if($input)
					$spell = $actor->getAbilitySet()->isValidSpell($input);
			}
			
			if(!$spell)
				return \Mechanics\Server::out($actor, "You don't know that spell.");
			
			// DETERMINE THE TARGET
			$target = null;
			
			if(isset($last))
				$target = $actor->getRoom()->getActorByInput($args);
			
			if(isset($last) && !$target)
				return \Mechanics\Server::out($actor, "You don't see them."); // Specified target not found
			
			if(!$target)
				$target = $actor->getTarget();
			
			// Target the caster
			if(!$target && $spell::getSpellType() == \Mechanics\Spell::TYPE_PASSIVE)
				$target = $actor;
			
			if(!$target)
				return \Mechanics\Server::out($actor, "Who do you want to cast that on?"); // No target specified and no default
			
			// CONCENTRATION
			if(rand(0, 100) > $spell->getPercent())
			{
				$actor->setMana($actor->getMana() - ceil($spell->getManaCost($actor->getLevel()) / 2));
				return \Mechanics\Server::out($actor, "You lost your concentration.");
			}
			
			$actors = $actor->getRoom()->getActors();
			foreach($actors as $rm_actor)
				if($rm_actor instanceof \Living\User)
					\Mechanics\Server::out($rm_actor, ($rm_actor->getId() == $actor->getId() ? 'You' : $actor->getAlias(true)) . ' utter' . ($rm_actor->getId() == $actor->getId() ? '' : 's') . ' the words, "' . $spell->getName($actor, $rm_actor) . '"');
			
			$actor->setMana($actor->getMana() - $spell->getManaCost($actor->getLevel()));
			
			$spell::perform($actor, $target);
			
			if($spell::getSpellType() == \Mechanics\Spell::TYPE_OFFENSIVE && $actor != $target)
				$actor->registerAttackRound($target);
		}
	}
?>
