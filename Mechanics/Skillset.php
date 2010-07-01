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
	class Skillset
	{
		
		private static $instances = array();
		private $actor = null;
		private $skills = array();
		
		protected function __construct(Actor $actor)
		{
		
			$this->actor = $actor;
			
			if(!($this->actor instanceof \Living\User))
				return;
			
			$rows = Db::getInstance()->query('SELECT * FROM skillsets WHERE fk_user_id = ?', $this->actor->getId())->fetch_objects();
			foreach($rows as $row)
			{
				$skill = 'Skills\\' . ucfirst($row->skill);
				$instance = new $skill($row->percent, $row->fk_user_id);
				$aliases = $skill::getAliases();
				
				if(!is_array($aliases))
					throw new \Exceptions\Skillset('Expecting array of aliases', Exceptions\Skillset::BAD_CONFIG);
				
				foreach($aliases as $alias)
					$this->skills[$alias] = $instance;
			}
		}
		
		public static function findByActor(Actor $actor)
		{
		
			$i = $actor->getAlias();
			if(!isset(self::$instances[$i]))
				self::$instances[$i] = new self($actor);
			
			return self::$instances[$i];
		}
		
		public function isValidSkill($input)
		{
		
			return isset($this->skills[$input]);
		}
		
		public function perform($args)
		{
		
			if(!$this->isValidSkill($args[0]))
				throw new Exceptions\Skillset('Skill not found.', Exceptions\Skillset::SKILL_NOT_FOUND);
			
			$this->skills[$args[0]]->perform($this->actor, $args);
		}
	}
?>
