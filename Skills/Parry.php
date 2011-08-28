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
	class Parry extends \Mechanics\Skill
	{
	
		protected $creation_points = 5;
		protected $is_performable = false;
		protected $ability_hook = \Mechanics\Ability::HOOK_HIT_DEFEND;
	
		protected function __construct()
		{
			$this->alias = new \Mechanics\Alias('parry', $this);
			parent::__construct();
		}
	
		public function perform(\Mechanics\Actor $actor, $chance = 0, $args = null)
		{
		
			$weapon = $this->getEquipped()->getEquipmentByPosition(Equipped::POSITION_WIELD_R);
			if(!$weapon)
				return false;
		
			$roll = \Mechanics\Server::chance();
			switch($actor->getSize())
			{
				case \Mechanics\Race::SIZE_TINY:
					$roll -= 0.10;
					break;
				case \Mechanics\Race::SIZE_SMALL:
					$roll -= 0.05;
					break;
				case \Mechanics\Race::SIZE_LARGE:
					$roll += 0.05;
					break;
			}
			
			$roll += $this->getNormalAttributeModifier($actor->getDex());
			
			$roll *= 1.25;
			
			if($roll < $chance)
			{
				Server::out($actor, $args->getAlias(true) . ' parries your attack!');
				Server::out($args, 'You parry ' . $actor->getAlias() . "'s attack!");
				return true;
			}
		}
	
	}

?>
