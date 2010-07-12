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
		
		private static $instances = array();
		private $actor = null;
		private $abilities = array();
		
		protected function __construct(Actor $actor)
		{
		
			$this->actor = $actor;
			
			if(!($this->actor instanceof \Living\User))
				return;
			
			$rows = Db::getInstance()->query('SELECT * FROM abilities WHERE fk_user_id = ?', $this->actor->getId())->fetch_objects();
			foreach($rows as $row)
			{
				
				$ability = $row->type == Ability::TYPE_SKILL ? 'Skills' : 'Spells';
				
				$ability = $ability . '\\' . ucfirst($row->name);
				$instance = new $ability($row->percent, $row->fk_user_id);
				$this->addAbility($instance);
			}
		}
		
		public static function findByActor(Actor $actor)
		{
		
			$i = $actor->getAlias();
			if(!isset(self::$instances[$i]))
				self::$instances[$i] = new self($actor);
			
			return self::$instances[$i];
		}
		
		public function addAbility(Ability $instance)
		{
			
			$aliases = $instance::getAliases();
				
			if(!is_array($aliases))
				throw new \Exceptions\Ability_Set('Expecting array of aliases', Exceptions\Ability_Set::BAD_CONFIG);
			
			$i = $instance->getType();
			
			if(isset($this->abilities[$i][$aliases[0]]))
				return;
			
			foreach($aliases as $alias)
				$this->abilities[$i][$alias] = $instance;
		}
		
		public function isValidSkill($input)
		{
		
			return isset($this->abilities[Ability::TYPE_SKILL][$input]) ? $this->abilities[Ability::TYPE_SKILL][$input] : null;
		}
		
		public function isValidSpell($input)
		{
		
			return isset($this->abilities[Ability::TYPE_SPELL][$input]) ? $this->abilities[Ability::TYPE_SPELL][$input] : null;
		}
		
		//public function perform(Ability $ability, $args)
		//{
		
			//$ability->perform($this->actor, $args);
			//if(!$this->isValidSkill($args[0]) && !$this->isValidSpell($args[0]))
			//	throw new Exceptions\Ability_Set('Ability not found.', Exceptions\Ability_Set::ABILITY_NOT_FOUND);
			
			//$i = $this->isValidSkill($args[0]) ? Ability::TYPE_SKILL : Ability::TYPE_SPELL;
			
			//$this->skills[$i][$args[0]]->perform($this->actor, $args);
		//}
		
		public function save()
		{
		
			foreach($this->abilities as $ability_type)
			{
				$uniques = array_unique($ability_type);
				foreach($uniques as $unique)
					$unique->save();
			}
		}
	}
?>
