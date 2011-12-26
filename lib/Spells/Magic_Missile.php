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
	use \Mechanics\Ability\Spell,
		\Mechanics\Actor,
		\Mechanics\Server;

	class Magic_Missile extends Spell
	{
		protected $is_offensive = true;
		protected $proficiency = 'sorcery';
		protected $required_proficiency = 20;

		protected function __construct()
		{
			self::addAlias('magic missile', $this);
		}
		
		public function perform(Actor $caster, Actor $target, $proficiency, $args = [])
		{
			$damage = round(rand($proficiency / 10, $proficiency / 5)); 
			$target->setHp($target->getHp() - $damage);
			Server::out($actor, "You smite " . $target->getAlias() . '!');
		}
	}

?>
