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
	class Meditation extends \Mechanics\Skill
	{
	
		protected $creation_points = 5;
		protected $is_performable = false;
		protected $ability_hook = \Mechanics\Ability::HOOK_TICK;
	
		protected function __construct()
		{
			$this->alias = new \Mechanics\Alias('meditation', $this);
			parent::__construct();
		}
	
		public function perform(\Mechanics\Actor $actor, $chance = 0, $args = null)
		{
		
			if($actor->getDisposition() === \Mechanics\Actor::DISPOSITION_FIGHTING)
				return;
		
			$roll = \Mechanics\Server::chance();
			
			$p = $actor->getDisciplineFocus();
			if($p instanceof \Disciplines\Cleric)
				$roll -= 0.25;
			
			$roll += $this->getEasyAttributeModifier($actor->getWis());
			
			if($roll < $chance)
			{
				$f = $actor->getDisciplineFocus();
				switch($f)
				{
					case ($f instanceof \Disciplines\Warrior):
						$hp_mod = rand(12, 18) / 100;
						$mv_mod = rand(8, 12) / 100;
						$ma_mod = 0;
					case ($f instanceof \Disciplines\Thief):
						$mv_mod = rand(12, 18) / 100;
						$hp_mod = rand(8, 12) / 100;
						$ma_mod = 0;
					case ($f instanceof \Disciplines\Mage):
					case ($f instanceof \Disciplines\Cleric):
						$ma_mod = rand(15, 20) / 100;
						$hp_mod = 0;
						$mv_mod = 0;
				}
				$actor->setHp($actor->getHp() + ($actor->getMaxHp() * $hp_mod));
				$actor->setMana($actor->getMana() + ($actor->getMaxMana() * $ma_mod));
				$actor->setMovement($actor->getMovement() + ($actor->getMovement() * $mv_mod));
			}
		}
	
	}

?>
