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
        private $actor = null;

        public function __construct(Actor $actor = null)
        {
            $this->actor = $actor;
        }
		
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

		public function addSpellGroup(Spell_Group $spell_group)
		{
			$alias = $spell_group::getAlias();
			if(!isset($this->spell_groups[$alias]))
				$this->spell_groups[$alias] = $spell_group;
		}
		
		public function applySkillsByHook($hook, $args)
		{
            foreach($this->skills as $sk)
				if($sk->getHook() === $hook && $sk->perform($this->actor, $args))
                        return true;
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

		public function getSpellGroupByAlias($alias)
		{
			if(isset($this->spell_groups[$alias]))
				return $this->spell_groups[$alias];
			return null;
		}

		public function getSkillByInput($input)
		{
            $matches = array();
            foreach($this->skills as $sk)
            {
                if($sk::getAlias() === $input)
                    return $sk;
                if(strpos($sk::getAlias(), $input) === 0)
                    $matches[] = $sk;
            }
            if($matches)
                return array_shift($matches);
		}

		public function getSpellGroupByInput($input)
		{
            $matches = array();
            foreach($this->spell_groups as $sg)
            {
                if($sg::getAlias() === $input)
                    return $sg;
                if(strpos($sg::getAlias(), $input) === 0)
                    $matches[] = $sg;
            }
            if($matches)
                return array_shift($matches);
		}

		public function removeSkill(Skill $skill)
		{
			if(isset($this->skills[$skill::getAlias()]))
			{
				unset($this->skills[$skill::getAlias()]);
				$this->skills = array_values($this->skills);
			}
		}

		public function removeSpellGroup(Spell_Group $spell_group)
		{
			if(isset($this->spell_groups[$spell_group::getAlias()]))
			{
				unset($this->spell_groups[$spell_group::getAlias()]);
				$this->spell_groups = array_values($this->spell_groups);
			}
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
