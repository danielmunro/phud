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
	namespace Items;
	class Food extends Item
	{
	
		protected $nourishment = 0;

		public function __construct($id, $long, $short, $nouns, $value, $weight, $nourishment = 1, $can_own = true, $door_unlock_id = null, $affects = '')
		{
			
			parent::__construct($id, $long, $short, $nouns, $value, $weight, self::TYPE_FOOD, $can_own, $affects);
			$this->nourishment = $nourishment;
		}
		
		public function save($inv_inside_id)
		{
		
			if($this->id)
				return \Mechanics\Db::getInstance()->query(
					'UPDATE items SET
						short_desc = ?,
						long_desc = ?,
						nouns = ?,
						value = ?,
						weight = ?,
						item_type = ?,
						can_own = ?,
						nourishment = ?,
						fk_inv_inside_id = ?,
						fk_door_unlock_id = ?
					WHERE
						id = ?', array($this->short, $this->long, $this->nouns, $this->value,
						$this->weight, $this->type, $this->can_own, $this->nourishment, $inv_inside_id, 
						$this->door_unlock_id, $this->id));
			
			\Mechanics\Db::getInstance()->query(
				'INSERT INTO items (
					short_desc,
					long_desc,
					nouns,
					value,
					weight,
					item_type,
					can_own,
					nourishment,
					fk_inv_inside_id,
					fk_door_unlock_id)
				VALUES
					(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
				array($this->short, $this->long, $this->nouns, $this->value, $this->weight,
				$this->type, $this->can_own, $this->nourishment, $inv_inside_id, $this->door_unlock_id));
			$this->id = \Mechanics\Db::getInstance()->insert_id;
		}
		
		public function getNourishment() { return $this->nourishment; }
	}

?>
