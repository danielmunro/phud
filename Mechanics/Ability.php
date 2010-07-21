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
	class Ability
	{
	
		private $name = '';
		private $percent = 0;
		private $user_id = 0;
		private $type = 0;
		
		// Level of the ability: when the actor can use it among other things
		protected static $level = 1;
		
		const TYPE_SKILL = 1;
		const TYPE_SPELL = 2;
	
		public function __construct($percent, $user_id = null)
		{
		
			$this->name = (string)$this;
			$this->percent = $percent;
			$this->user_id = $user_id;
			$this->type = strpos(get_class($this), 'Skills') === 0 ? self::TYPE_SKILL : self::TYPE_SPELL;
		}
		
		public function save()
		{
			if($this->user_id)
				Db::getInstance()->query('
					INSERT INTO abilities (`name`, percent, fk_user_id, `type`) VALUES (?, ?, ?, ?)
					ON DUPLICATE KEY UPDATE percent = ?', array($this->name, $this->percent, $this->user_id, $this->type, $this->percent));
		}
	
		public function getType() { return $this->type; }
		public function getName() { return $this->name; }
		public function getPercent() { return $this->percent; }
		public function setPercent($percent) { $this->percent = $percent; }
		public static function getLevel() { return self::$level; }
		
		public function __toString()
		{
			$class = get_called_class();
			return substr($class, strpos($class, '\\') + 1);
		}
	}

?>
