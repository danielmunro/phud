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
	namespace Skills;
	use \Mechanics\Ability\Ability;
	use \Mechanics\Ability\Skill;
	use \Mechanics\Server;
    use \Disciplines\Warrior;
    use \Disciplines\Thief;

	class Third_Attack extends Skill
	{
	
		protected static $alias = 'third attack';
		protected static $level = 25;
		protected static $creation_points = 8;
		protected static $hook = Ability::HOOK_HIT_ATTACK_ROUND;
	
		public function perform($args = array())
		{
			$roll = Server::chance() + 50;
			
			$mod = $this->getEasyAttributeModifier($actor->getDex());
			$mod += $this->getEasyAttributeModifier($actor->getStr());
			
			$roll += $mod / 2;
			
			if($actor->getDisciplinePrimary() === Warrior::instance())
				$roll -= 15;
			else if($actor->getDisciplinePrimary() === Thief::instance())
				$roll -= 5;
			
			if($roll < $this->percent)
			{
				$actor->attack('3rd');
			}
		}
	}

?>