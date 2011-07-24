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
	class Spell_Group
	{
		
		const GROUP_PROTECTIVE = 1;
		const GROUP_HEALING = 2;
		const GROUP_TRANSPORTATION = 3;
		const GROUP_CURATIVE = 4;
		const GROUP_MALADICTIONS = 5;
		const GROUP_PLAGUE = 6;
		const GROUP_ELEMENTAL = 7;
		
		protected $creation_points = 0;
		protected $spells = array();
		private static $instances = array(
			self::GROUP_PROTECTIVE => null,
			self::GROUP_HEALING => null,
			self::GROUP_TRANSPORTATION => null,
			self::GROUP_CURATIVE => null,
			self::GROUP_MALADICTIONS => null,
			self::GROUP_PLAGUE => null,
			self::GROUP_ELEMENTAL => null
		);
		private $instance_type = 0;
		private $spells = array();
		
		protected function __construct($instance_type)
		{
			$this->instance_type = $instance_type;
		}
		
		public static function instance()
		{
			if(!isset(static::$instance))
				static::$instance = new static();
			return static::$instance;
		}
		
		public function getSpells()
		{
			return $this->spells;
		}
		
		public function getCreationPoints()
		{
			return $this->creation_points;
		}
	}
?>
