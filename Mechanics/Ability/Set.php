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
	class Set
	{
		
		private $skills = array();
		private $spell_groups = array();

		private static $instance = null;
		
		public function getSkills()
		{
			return $this->skills;
		}
		public function getSpells()
		{
			$spells = array();
			array_walk(
				$this->spell_groups,
				function($g) use (&$spells)
				{
					$spells = array_merge($g->getSpells(), $spells);
				}
			);
			return $spells;
		}
		
		public function getSpellGroups()
		{
			return $this->spell_groups;
		}

		public function addSkill(Skill $skill)
		{
			$alias = $skill::getAlias();
			if(!isset($this->skills[$alias]))
				$this->skills[$alias] = $skill;
		}

		public function addSpellGroup($spell_group)
		{
			$alias = $spell_group::getAlias();
			if(!isset($this->spell_groups[$alias])
				$this->spell_groups[$alias] = $spell_group;
		}
		
		public function getAbilitiesByHook($hook)
		{
			$abilities = array_merge($this->skills, $this->getSpells());
			return array_filter($abilities, function($a) use ($hook)
				{
					return $a->getHook() === $hook;
				});
		}

		public function getSkillByAlias($alias)
		{
			if(isset($this->skills[$alias]))
				return $this->skills[$alias];
			return null;
		}

		public function getSpellByAlias($alias)
		{
			foreach($this->spell_groups as $g)
			{
				$spell = $g->getSpellByAlias($alias);
				if($spell)
					return $spell;
			}
			return null;
		}
		
		public function getCreationPoints()
		{
			$creation_points = 0;
			$abilities = array_merge($this->skills, $this->spells);
			array_walk(
				$abilities,
				function($a) use (&$creation_points)
				{
					$creation_points += $a->getCreationPoints();
				}
			);	
			return $creation_points;
		}
	}
?>
