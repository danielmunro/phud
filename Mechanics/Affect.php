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
		private $message_affect = '';
		private $message_end = '';
		private $timeout = 0;
		private $args = array();
		private $attributes = null;
		
		public function __construct()
		{
			$this->attributes = new Attributes();
		}
		public function getAttributes()
		{
			return $this->attributes;
		}
		public function setAffect($affect)
		{
			$this->affect = $affect;
		}
		public function getAffect()
		{
			return $this->affect;
		}
		public function setMessageAffect($message)
		{
			$this->message_affect = $message;
		}
		public function getMessageAffect()
		{
			return $this->message_affect;
		}
		public function setMessageEnd($message)
		{
			$this->message_end = $message;
		}
		public function getMessageEnd()
		{
			return $this->message_end;
		}
		public function setTimeout($timeout)
		{
			$this->timeout = $timeout;
		}
		public function getTimeout()
		{
			return $this->timeout;
		}
		public function decreaseTime()
		{
			if($this->timeout > 0)
				$this->timeout--;
		}
		public function setArgs($args)
		{
			$this->args = $args;
		}
		public function getArgs($i = '')
		{
			if($i)
				return $this->args[$i];
			else
				return $this->args;
		}
		public static function reapplyFromDb(&$target, $table = '')
		{
			if(!$table && $target instanceof \Items\Item)
				$table =  'items';
			$rows = Db::getInstance()->query('SELECT * FROM affects WHERE fk_table = ? AND fk_id = ?', array($table, $target->getId()))->fetch_objects();
			Debug::addDebugLine("AFFECT COUNT: ".sizeof($rows));
			foreach($rows as $row)
			{
				$a = new Affect();
				$a->setAffect($row->affect);
				$a->setMessageAffect($row->message_affect);
				$a->setMessageEnd($row->message_end);
				$a->setTimeout($row->timeout);
				$a->setArgs(unserialize($row->args));
				$target->addAffect($a);
			}
		}
		public function save($table, $id)
		{
			Db::getInstance()->query('INSERT INTO affects (fk_table, fk_id, affect, message_affect, message_end, timeout, args) VALUES (?, ?, ?, ?, ?, ?, ?)
				ON DUPLICATE KEY UPDATE timeout = ?', array($table, $id, $this->affect, $this->message_affect, $this->message_end, $this->timeout, 
				serialize($this->args), $this->timeout));
		}
		public function removeFromDb($table, $id)
		{
			Db::getInstance()->query('DELETE FROM affects WHERE fk_table = ? AND fk_id = ? AND affect = ?', array($table, $id, $this->affect));
		}
	}
?>
