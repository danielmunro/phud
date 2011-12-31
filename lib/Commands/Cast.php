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
		\Mechanics\Event\Subscriber,
		\Mechanics\Event\Event,
		\Mechanics\Command\Command,
		\Mechanics\Ability\Ability,
		\Mechanics\Ability\Spell as mSpell,
		\Mechanics\Fighter as mFighter,
		\Living\User as lUser;

	class Cast extends Command
	{
		protected $dispositions = [Actor::DISPOSITION_STANDING];
		
		protected function __construct()
		{
			self::addAlias('cast', $this, 11);
		}
		
		public function perform(Actor $actor, $args = [], Subscriber $command_subscriber)
		{
			/**
			 * STEP 1
			 * Parse the input from the user. This includes resolving the spell and the target from
			 * the provided input.
			 */

			array_shift($args); // get rid of the command part of the args
			$s = sizeof($args);
			$arg_spell_casting = null;
			$target = null;

			if($s === 1) {
				$arg_spell_casting = Ability::lookup(trim($args[0], "'"));
				$target = $actor;
			} else {
				$arg_target = $args[$s-1];
				$arg_target_lookup = $actor->getRoom()->getActorByInput($arg_target);
				if($arg_target_lookup) {
					$target = $arg_target_lookup;
					array_pop($args); // remove the target from the casting string
				} else {
					$target = $actor;
				}
				$arg_spell_casting = Ability::lookup(trim(implode(' ', $args), "'"));
			}

			/**
			 * STEP 2
			 * Sanity checks. Make sure we found both a target and a spell and the caster has
			 * enough mana to cast the spell.
			 */

			// Check if the spell exists
			if(empty($arg_spell_casting) || !($arg_spell_casting['lookup'] instanceof mSpell)) {
				return Server::out($actor, "That spell does not exist in this realm.");
			}

			// Check if the target exists
			if(empty($target)) {
				return Server::out($actor, "Cast ".$arg_spell_casting['alias']." on who?");
			}

			// Does the caster actually know the spell?
			if(!in_array($arg_spell_casting['alias'], $actor->getAbilities())) {
				return Server::out($actor, "You do not know that spell.");
			}

			// Does the caster have sufficient mana?
			$cost = $arg_spell_casting['lookup']->getManaCost($actor->getProficiencyIn($arg_spell_casting['lookup']->getProficiency()));
			if($cost > $actor->getMana()) {
				return Server::out($actor, "You lack the mana to cast ".$arg_spell_casting['alias']." right now.");
			}

			/**
			 * STEP 3
			 * Increment the delay, do a proficiency roll, calculate saves, and perform.
			 */

			$actor->incrementDelay($arg_spell_casting['lookup']->getDelay());

			// Caster will roll to see if they lose concentration
			if(!$arg_spell_casting['lookup']->checkProficiencyRoll($actor)) {
				$actor->setMana($actor->getMana()-(round($cost/2)));
				return Server::out($actor, "You lost your concentration.");
			}

			$actor->setMana($actor->getMana()-$cost);

			// This event announces the beginning of battle, allowing for the target to have an observer that cancels the fight
			if($arg_spell_casting['lookup']->isOffensive()) {
				if($actor->reconcileTarget($target)) {
					$target->fire(Event::EVENT_ATTACKED, $actor, $command_subscriber);
					if($command_subscriber->isSuppressed()) {
						return;
					}
				}
			}

			// Determine saving throws on this cast and perform
			$modifier = 1;
			$saves = $arg_spell_casting['lookup']->calculateSaves($actor, $target);
			$actor->fire(Event::EVENT_CAST, $target, $arg_spell_casting, $modifier, $saves);
			$modifier = Server::_range(0.1, 2, $modifier);
			$saves *= $modifier;
			$saves = Server::_range(5, 95, $saves);
			$arg_spell_casting['lookup']->perform($actor, $target, $actor->getProficiencyIn($arg_spell_casting['lookup']->getProficiency()), $saves);
		}
	}
?>
