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
	abstract class Spell extends Ability
	{
		protected static $min_mana = 15;
		protected static $spell_type = self::TYPE_PASSIVE;
	
		public static function getManaCost($actor_level)
		{
			return ceil(max(self::$min_mana, 100 / (2 + $actor_level - self::$level)));
		}
		
		public static function getAlias()
		{
			return static::$alias;
		}

		protected static function calculateStandardDamage($level, $min, $exponent)
		{
			$base = $min + ($level ^ $exponent);
			return ceil(rand($base / 2, $base * 2));
		}

		public static function getSpellType()
		{
			return static::$spell_type;
		}
		
		public function __toString()
		{
			return static::$alias;
		}	
	}

?>
