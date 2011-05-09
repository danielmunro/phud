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
	namespace Spells;
	class Magic_Missile extends \Mechanics\Spell
	{
	
		protected static $name_familiar = 'magic missile';
		protected static $name_unfamiliar = 'oqisasi';
		protected static $spell_type = self::TYPE_OFFENSIVE;
	
		public function __construct($percent, $actor_id = null, $actor_type = '')
		{
			parent::__construct($percent, self::TYPE_SPELL, $actor_id, $actor_type, array('magic missile', 'magic', 'mis', 'ma'));
		}
	
		public static function perform(\Mechanics\Actor &$actor, \Mechanics\Actor &$target, $args = null)
		{
			
			$target->setHp($target->getHp() - self::calculateStandardDamage($actor->getLevel(), 3, 0.7));
			\Mechanics\Server::out($actor, "You smite " . $target->getAlias() . '!');
		}
	}

?>
