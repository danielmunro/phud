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
	class Dodge extends \Mechanics\Skill
	{
	
		protected $creation_points = 5;
		protected $is_performable = false;
		protected $ability_hook = \Mechanics\Ability::HOOK_HIT_DEFEND;
	
		protected function __construct()
		{
			$this->alias = new \Mechanics\Alias('dodge', $this);
			parent::__construct();
		}
	
		public function perform(\Mechanics\Actor $actor, $chance = 0, $args = null)
		{
			
			$roll = \Mechanics\Server::chance();
			switch($actor->getSize())
			{
				case \Mechanics\Race::SIZE_TINY:
					$chance += 5;
					break;
				case \Mechanics\Race::SIZE_SMALL:
					$chance += 1;
					break;
				case \Mechanics\Race::SIZE_LARGE:
					$chance -= 5;
					break;
			}
			
			$roll += $this->getNormalAttributeModifier($actor->getDex());
			
			if($roll < $chance)
			{
				Server::out($actor, $args->getAlias(true) . ' dodges your attack!');
				Server::out($args, 'You dodge ' . $actor->getAlias() . "'s attack!");
				return true;
			}
			return false;
		}
	
	}

?>
