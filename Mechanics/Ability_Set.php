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
		private $skills = array();
		private $spell_groups = array();
		private $spells = array();
		
		public function __construct(Actor $actor = null)
		{
			if($actor)
				$this->loadActorAbilitySet($actor);
		}
		
		private function loadActorAbilitySet(Actor $actor)
		{
			/** Have to change how all this is done
			$this->actor = $actor;
			$rows = Db::getInstance()->query('SELECT * FROM abilities WHERE actor_type = ? AND fk_actor_id = ?', array($this->actor->getType(), $this->actor->getId()))->fetch_objects();
			foreach($rows as $row)
			{
				$ability = $row->type == Ability::TYPE_SKILL ? 'Skills' : 'Spells';
				$ability = $ability . '\\' . ucfirst($row->name);
				if($row->type == Ability::TYPE_SKILL)
					$learned = new Learned_Skill($ability::instance(), $actor, $row->percent);
				else
					$learned = new Learned_Spell_Group($ability::instance(), $actor, $row->percent);
				$this->addAbility($ability::instance());
			}
			*/
		}
		
		public function getSkills()
		{
			return
				array_filter($this->abilities, function($ability)
				{
					return $ability->getType() === Ability::TYPE_SKILL;
				});
		}
		public function getSpells()
		{
			return
				array_filter($this->abilities, function($ability)
				{
					return $ability->getType() === Ability::TYPE_SPELL;
				});
		}
		
		public function addAbilities($abilities)
		{
			foreach($abilities as $ability)
				$this->addAbility($ability);
		}
		
		public function addAbility(Ability $instance)
		{
			$this->abilities[] = new Learned_Ability($this->actor, $instance);
		}
		
		public function getLearnedAbility($ability)
		{
			foreach($this->abilities as $learned_ability)
				if($this->learned_ability->getAbility() == $ability)
					return $learned_ability;
			return null;
		}
		
		public function save()
		{
			foreach($this->skills as $learned_skill)
				$learned_skill->save();
			foreach($this->spells as $learned_spell)
				$learned_spell->save();
			
		}
		
		public function getCreationPoints()
		{
			$creation_points = 0;
			foreach($this->skills as $skill)
				$creation_points += $skill->getCreationPoints();
			foreach($this->spell_groups as $group)
				$creation_points += $group->getCreationPoints();
			return $creation_points;
		}
	}
?>
