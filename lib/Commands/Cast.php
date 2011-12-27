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

			if(empty($arg_spell_casting) || !($arg_spell_casting['lookup'] instanceof mSpell)) {
				return Server::out($actor, "That spell does not exist in this realm.");
			}

			$cost = $arg_spell_casting['lookup']->getManaCost($actor->getProficiencyIn($arg_spell_casting['lookup']->getProficiency()));
			if($cost > $actor->getMana()) {
				return Server::out($actor, "You lack the mana to cast ".$arg_spell_casting['alias']." right now.");
			}
			$actor->setMana($actor->getMana()-$cost);

			if(empty($target)) {
				return Server::out($actor, "Cast ".$arg_spell_casting['alias']." on who?");
			}

			if(!in_array($arg_spell_casting['alias'], $actor->getAbilities())) {
				return Server::out($actor, "You do not know that spell.");
			}


			if($arg_spell_casting['lookup']->isOffensive()) {
				if($actor->reconcileTarget($target)) {
					$actor->getTarget()->fire(Event::EVENT_ATTACKED, $actor, $command_subscriber);
					if($command_subscriber->isSuppressed()) {
						return;
					}
				}
			}
			$arg_spell_casting['lookup']->perform($actor, $target, $actor->getProficiencyIn($arg_spell_casting['lookup']->getProficiency()));
			return;
		}
	}
?>
