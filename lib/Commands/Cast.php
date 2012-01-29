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
			$s = sizeof($args);
			if($s === 2) {
				$arg_spell_casting = Ability::lookup(implode(' ', array_slice($args, 1)));
			} else if($s > 2) {
				$arg_spell_casting = Ability::lookup(implode(' ', array_slice($args, 1, $s-2)));
			}

			// Check if the spell exists
			if(empty($arg_spell_casting) || !($arg_spell_casting['lookup'] instanceof mSpell)) {
				return Server::out($actor, "That spell does not exist in this realm.");
			}

			// Does the caster actually know the spell?
			if(!in_array($arg_spell_casting['alias'], $actor->getAbilities())) {
				return Server::out($actor, "You do not know that spell.");
			}

			// This event announces the beginning of battle, allowing for the target to have an observer that cancels the fight
			// @TODO move this to the ability classes
			/**
			if($arg_spell_casting['lookup']->isOffensive()) {
				if($actor->reconcileTarget($target)) {
					$target->fire(Event::EVENT_ATTACKED, $actor, $command_subscriber);
					if($command_subscriber->isSuppressed()) {
						return;
					}
				}
			}
			*/

			$arg_spell_casting['lookup']->perform($actor, $args);
		}
	}
?>
