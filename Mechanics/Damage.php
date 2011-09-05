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
	class Damage
	{
		const TYPE_HIT = 0;
		const TYPE_BASH = 1;
		const TYPE_SLASH = 2;
		const TYPE_PIERCE = 3;
		const TYPE_MAGIC = 4;
		const TYPE_BACKSTAB = 5;
		const TYPE_POUND = 6;
		
		public static function getDamageTypeLabel($damage_type)
		{
			switch($damage_type)
			{
				case self::TYPE_HIT:
					return 'hit';
				case self::TYPE_BASH:
					return 'bash';
				case self::TYPE_SLASH:
					return 'slash';
				case self::TYPE_PIERCE:
					return 'pierce';
				case self::TYPE_MAGIC:
					return 'magic';
				case self::TYPE_BACKSTAB:
					return 'backstab';
				case self::TYPE_POUND:
					return 'pound';
			}
		}
	}
?>
