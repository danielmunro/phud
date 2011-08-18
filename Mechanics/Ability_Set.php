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
	class Ability_Set
	{
		
		private $actor = null;
		private $abilities = array();
		private $spell_groups = array();
		
		public function __construct(Actor $actor = null)
		{
			$this->actor = $actor;
		}
		
		public function getSkills()
		{
			return
				array_filter($this->abilities, function($learned)
				{
					return $learned->getAbility()->getType() === Ability::TYPE_SKILL;
				});
		}
		public function getSpells()
		{
			return
				array_filter($this->abilities, function($learned)
				{
					return $learned->getAbility()->getType() === Ability::TYPE_SPELL;
				});
		}
		
		public function getSpellGroups()
		{
			return $this->spell_groups;
		}
		
		public function getAbilities()
		{
			return $this->abilities;
		}
		
		public function addAbilities($abilities)
		{
			foreach($abilities as $ability)
			{
				if($ability instanceof Learned_Ability)
					$lookup = $ability->getAbility();
				else
					$lookup = $ability;
				$this->addAbility($lookup);
			}
		}
		
		public function addAbility(Ability $instance, $percent = 1)
		{
			// Only add it if they don't already have it
			if(!$this->getLearnedAbility($instance))
			{
				$this->abilities[] = new Learned_Ability($instance, $this->actor, $percent);
				if($instance instanceof Spell)
				{
					$spell_group_alias = $instance->getSpellGroup()->getAlias()->getAliasName();
					if(!in_array($spell_group_alias, $this->spell_groups))
						$this->spell_groups[] = $spell_group_alias;
				}
			}
		}
		
		public function getLearnedAbility($ability)
		{
			if($ability instanceof Spell_Group)
				return in_array($ability, $this->spell_groups);
			else if($ability instanceof Ability)
			{
				$found = array_filter($this->abilities, function($a) use ($ability)
					{
						return $a->getAbility() === $ability;
					});
				if($found)
					return array_shift($found);
			}
			return null;
		}
		
		public function getAbilitiesByHook($hook)
		{
			return array_filter($this->abilities, function($a) use ($hook)
				{
					return $a->getAbility()->getHook() === $hook;
				});
		}
		
		public function getCreationPoints()
		{
			$creation_points = 0;
			$spell_groups = array();
			foreach($this->abilities as $learned_ability)
			{
				$ability = $learned_ability->getAbility();
				if($ability instanceof Skill)
					$creation_points += $ability->getCreationPoints();
				else if($ability instanceof Spell && !in_array($ability->getSpellGroup()->getAlias()->getAliasName(), $spell_groups))
				{
					$spell_groups[] = $ability->getSpellGroup()->getAlias()->getAliasName();
					$creation_points += $ability->getCreationPoints();
				}
			}
			return $creation_points;
		}
	}
?>
