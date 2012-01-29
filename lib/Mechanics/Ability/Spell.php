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
	namespace Mechanics\Ability;
	use \Mechanics\Actor,
		\Mechanics\Server;

	abstract class Spell extends Ability
	{
		protected $initial_mana_cost = 50;
		protected $min_mana_cost = 15;
	
		public function getManaCost($proficiency)
		{
			$min = round(($this->initial_mana_cost - $proficiency) / 10) * 10;
			return max($min, $this->min_mana_cost);
		}

		protected function applyCost(Actor $actor)
		{
			$mana_cost = $this->getManaCost($actor->getProficiencyIn($this->proficiency));
			if($actor->getAttribute('mana') < $mana_cost) {
				Server::out($actor, "You lack the mana to do that.");
				return false;
			}
			$actor->modifyAttribute('mana', -($mana_cost));
		}

		protected function fail(Actor $actor)
		{
			Server::out($actor, "You lost your concentration.");
		}

		protected function determineTarget(Actor $actor, $args)
		{
			$s = sizeof($args);
			if($s === 2) {
				return $actor;
			} else if($s > 2) {
				// Spells, unlike skills, can target the actor performing the ability
				$target = $actor->getRoom()->getActorByInput(array_slice($args, -1)[0]);
				if($target) {
					return $target;
				}
			}
		}
	}
?>
