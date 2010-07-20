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
	namespace Mechanics;
	class Spell extends \Mechanics\Ability
	{
	
		protected $level = 1;
		protected $name_familiar = '';
		protected $name_unfamiliar = '';
		protected $min_mana = 15;
	
		public function getManaCost($actor_level)
		{
			return max($this->min_mana, 100 / (2 + $actor_level - $this->level));
		}
		
		public function getLevel() { return $this->level; }
		public function getNameFamiliar() { return $this->name_familiar; }
		public function getNameUnfamiliar() { return $this->name_unfamiliar; }
		public function getName(\Mechanics\Actor $caster, \Mechanics\Actor $observer)
		{
			if($observer->getLevel() >= $this->level && $observer->getDiscipline() == $caster->getDiscipline())
				return $this->name_familiar;
			else
				return $this->name_unfamiliar;
		}
		public function __toString()
		{
			$class = get_class($this);
			return substr($class, strpos($class, '\\') + 1);
		}
	}

?>
