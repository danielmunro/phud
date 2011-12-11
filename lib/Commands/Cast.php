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
		\Mechanics\Actor,
		\Mechanics\Server,
		\Mechanics\Command\Command,
		\Mechanics\Spell as mSpell,
		\Mechanics\Fighter as mFighter,
		\Living\User as lUser;

	class Cast extends Command
	{
		protected $dispositions = array(Actor::DISPOSITION_STANDING);
		
		protected function __construct()
		{
			new Alias('cast', $this);
		}
		
		public function perform(Actor $actor, $args = array())
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
				return Server::out($actor, "You don't know that spell.");
			
			// DETERMINE THE TARGET
			$target = null;
			
			if(isset($last))
				$target = $actor->getRoom()->getActorByInput($args);
			
			if(isset($last) && !$target)
				return Server::out($actor, "You don't see them."); // Specified target not found
			
			if(!$target)
				$target = $actor->getTarget();
			
			// Target the caster
			if(!$target && $spell::getSpellType() == mSpell::TYPE_PASSIVE)
				$target = $actor;
			
			if(!$target)
				return Server::out($actor, "Who do you want to cast that on?"); // No target specified and no default
			
			if(!($target instanceof mFighter) && $spell::getSpellType() == Spell::TYPE_OFFENSIVE) // Can't cast an offensive spell on a non fighter
				return Server::out($actor, "They wouldn't like that very much.");
			
			// CONCENTRATION
			if(rand(0, 100) > $spell->getPercent())
			{
				$actor->setMana($actor->getMana() - ceil($spell->getManaCost($actor->getLevel()) / 2));
				return Server::out($actor, "You lost your concentration.");
			}
			
			$actors = $actor->getRoom()->getActors();
			foreach($actors as $rm_actor)
				if($rm_actor instanceof lUser)
					Server::out($rm_actor, ($rm_actor == $actor ? 'You' : $actor->getAlias(true)) . ' utter' . ($rm_actor == $actor ? '' : 's') . ' the words, "' . $spell->getName($actor, $rm_actor) . '"');
			
			$actor->setMana($actor->getMana() - $spell->getManaCost($actor->getLevel()));
			
			$spell::perform($actor, $target);
			
			if($spell::getSpellType() == mSpell::TYPE_OFFENSIVE && $actor != $target)
				$actor->reconcileTarget($target);
		}
	}
?>
