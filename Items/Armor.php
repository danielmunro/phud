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

	class Armor extends Equipment
	{
	
		protected $ac_slash = 0;
		protected $ac_bash = 0;
		protected $ac_pierce = 0;
		protected $ac_magic = 0;
		
		public function __construct($id, $long, $short, $nouns, $value, $weight, $type, $ac_slash, $ac_bash, $ac_pierce, $ac_magic, $condition = 100, $can_own = true, $door_unlock_id = null)
		{
			
			parent::__construct($id, $long, $short, $nouns, $value, $weight, $type, $condition, $can_own, $door_unlock_id);
			$this->ac_slash = $ac_slash;
			$this->ac_bash = $ac_bash;
			$this->ac_pierce = $ac_pierce;
			$this->ac_magic = $ac_magic;
		}
		public function save($inv_inside_id)
		{
			if($this->id)
				return Db::getInstance()->query(
					'UPDATE items SET
						short_desc = ?,
						long_desc = ?,
						nouns = ?,
						value = ?,
						weight = ?,
						item_type = ?,
						can_own = ?,
						fk_inv_inside_id = ?,
						fk_door_unlock_id = ?,
						ac_slash = ?,
						ac_bash = ?,
						ac_pierce = ?,
						ac_magic = ?
					WHERE
						id = ?', array($this->short, $this->long, $this->nouns, $this->value,
						$this->weight, $this->type, $this->can_own, $inv_inside_id,
						$this->door_unlock_id, $this->ac_slash, $this->ac_bash, $this->ac_pierce, $this->ac_magic, $this->id));
			
			Db::getInstance()->query(
				'INSERT INTO items (
					short_desc,
					long_desc,
					nouns,
					value,
					weight,
					item_type,
					can_own,
					fk_inv_inside_id,
					fk_door_unlock_id,
					ac_slash,
					ac_bash,
					ac_pierce,
					ac_magic)
				VALUES
					(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
				array($this->short, $this->long, $this->nouns, $this->value, $this->weight,
				$this->type, $this->can_own, $inv_inside_id, $this->door_unlock_id, $this->ac_slash,
				$this->ac_bash, $this->ac_pierce, $this->ac_magic));
			$this->id = Db::getInstance()->insert_id;
		}
		public function getACSlash() { return $this->ac_slash; }
		public function getACBash() { return $this->ac_bash; }
		public function getACPierce() { return $this->ac_pierce; }
		public function getACMagic() { return $this->ac_magic; }
	
	}

?>
