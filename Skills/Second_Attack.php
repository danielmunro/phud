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
	class Second_Attack extends \Mechanics\Skill
	{
	
		protected $creation_points = 8;
		protected $hook = \Mechanics\Ability::HOOK_HIT_ATTACK_ROUND;
	
		protected function __construct()
		{
			$this->alias = new \Mechanics\Alias('second attack', $this);
			parent::__construct();
		}
	
		public function perform(\Mechanics\Actor $actor, $chance = 0, $args = array())
		{
			$roll = \Mechanics\Server::chance() + 25;
			
			$mod = $this->getEasyAttributeModifier($actor->getDex());
			$mod += $this->getEasyAttributeModifier($actor->getStr());
			
			$roll += $mod / 2;
			
			if($actor->getDisciplinePrimary() === \Disciplines\Warrior::instance())
				$roll -= 15;
			else if($actor->getDisciplinePrimary() === \Disciplines\Thief::instance())
				$roll -= 5;
			
			return $roll < $chance;
		}
		
		public function getAttackName()
		{
			return '2nd';
		}
	}

?>
