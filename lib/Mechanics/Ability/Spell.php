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
	use \Mechanics\Actor;

	abstract class Spell extends Ability
	{
		protected $initial_mana_cost = 50;
		protected $min_mana_cost = 15;
		protected $is_offensive = false;
		protected $delay = 1;
	
		public function getManaCost($proficiency)
		{
			$min = round(($this->initial_mana_cost - $proficiency) / 10) * 10;
			return max($min, $this->min_mana_cost);
		}

		public function getDelay()
		{
			return $this->delay;
		}

		public function isOffensive()
		{
			return $this->is_offensive;
		}

		abstract public function perform(Actor $caster, Actor $target, $proficiency, $args = []);
	}
?>
