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
	class Affect
	{
	
		const GLOW = 'glow';
		const STUN = 'stun';
		
		private $affect = '';
		private $message = '';
		private $target = null;
		private $pulse_start = 0;
		private $args = array();
		private static $instances = array();
		
		public function __construct($affect, &$target, $message = '', $args = array())
		{
		
			$this->affect = $affect;
			$this->target = $target;
			$this->message = $message;
			$this->target->addAffect($this);
			$this->args = $args;
			
			$class = get_class($target);
			self::$instances[$class][$target->getId()][] = $this;
			
			if($this->args)
				$this->initialize();
		}
		public function initialize()
		{
			
			if($this->pulse_start)
				$this->args['timeout'] = $this->args['timeout'] - (Server::getLastPulse() - $this->pulse_start);
			else
				$this->pulse_start = Server::getLastPulse();
			
			if($this->args['timeout'] <= 0)
				return false;
			
			$affect = $this->affect;
			$affect::apply($this->target, $this->args, $this);
			
			return true;
		}
		public function getAffect() { return $this->affect; }
		public function getMessage() { return $this->message; }
		public function getTickTimeout()
		{
			
			return ceil(($this->args['timeout'] - (Server::getLastPulse() - $this->pulse_start)) / Server::PULSES_PER_TICK);
		}
		public static function reapplyFromMemory($target)
		{
			
			$class = get_class($target);
			
			if(!isset(self::$instances[$class][$target->getId()]))
				return;
			
			foreach(self::$instances[$class][$target->getId()] as $instance)
				if(!$instance->initialize())
					unset($instance);
		}
		public static function reapplyFromDb(&$target, $table = '')
		{
			if(!$table && $target instanceof \Items\Item)
				$table =  'items';
			else
				$table = '';
			$rows = Db::getInstance()->query('SELECT * FROM affects WHERE fk_table = ? AND fk_id = ?', array($table, $target->getId()), true)->fetch_objects();
			Debug::addDebugLine("AFFECT COUNT: ".sizeof($rows));
			foreach($rows as $row)
				new Affect($row->affect, $target);
		}
		public static function isAffecting($target, $affect)
		{
			foreach($target->getAffects() as $a)
				if($a->getAffect() == $affect)
					return true;
		}
		public function removeAffectFrom($target)
		{
			$target->removeAffect($this);
			$class = get_class($target);
			$i = array_search($this, self::$instances[$class][$target->getId()]);
			if($i !== false)
				unset(self::$instances[$class][$target->getId()][$i]);
		}
		public static function getAffects($target)
		{
			
			$class = get_class($target);
			
			if(isset(self::$instances[$class][$target->getId()]))
				return self::$instances[$class][$target->getId()];
			else
				return array();
		}
		public function save($table, $id)
		{
			Db::getInstance()->query('INSERT INTO affects (fk_table, fk_id, affect) VALUES (?, ?, ?)', array($table, $id, $this->affect));
		}
	}
?>
