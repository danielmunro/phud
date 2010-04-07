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

	class Food extends Item
	{
	
		protected $nourishment = 0;

		public function __construct($id, $long, $short, $nouns, $value, $weight, $condition, $nourishment, $shop = false)
		{
			$this->id = $id;
			$this->long = $long;
			$this->short = $short;
			$this->nouns = $nouns;
		
			$this->value = $value;
			$this->weight = $weight;
			$this->condition = $condition;
			$this->nourishment = $nourishment;
			$this->shop = $shop;
			$this->type = 'food';
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
						item_condition = ?,
						item_type = ?,
						can_own = ?,
						equipment_position = ?,
						verb = ?,
						nourishment = ?,
						fk_inv_inside_id = ?
					WHERE
						id = ?', array($this->short, $this->long, $this->nouns, $this->value,
						$this->weight, $this->condition, $this->type, $this->can_own,
						$this->equipment_position, $this->verb, $this->nourishment, $inv_inside_id, 
						$this->id));
			
			Db::getInstance()->query(
				'INSERT INTO items (
					short_desc,
					long_desc,
					nouns,
					value,
					weight,
					item_condition,
					item_type,
					can_own,
					equipment_position,
					verb,
					nourishment,
					fk_inv_inside_id)
				VALUES
					(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
				array($this->short, $this->long, $this->nouns, $this->value, $this->weight,
				$this->condition, $this->type, $this->can_own, $this->equipment_position,
				$this->verb, $this->nourishment, $inv_inside_id));
			$this->id = Db::getInstance()->insert_id;
		}
		public function getNourishment()
		{
			return $this->nourishment;
		}
	}

?>
