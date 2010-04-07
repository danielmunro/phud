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

	class Item
	{
	
		protected $id = 0;
		protected $short = '';
		protected $long = '';
		protected $nouns = '';
		protected $value = 0;
		protected $weight = 0.0;
		protected $condition = 100;
		protected $type = '';
		protected $can_own = true;
		protected $equipment_position = '';
		protected $verb = '';
		protected $shop = false;
		
		private static $instances = array();
		
		public function __construct($id, $long, $short, $nouns, $value, $weight, $condition, $type, $can_own = true, $verb = '', $equipment_position = null, $shop = false)
		{
		
			$this->id = $id;
			$this->long = $long;
			$this->short = $short;
			$this->nouns = $nouns;
		
			$this->value = $value;
			$this->weight = $weight;
			$this->condition = $condition;
			$this->type = $type;
			$this->verb = $verb;
			$this->can_own = $can_own;
			$this->equipment_position = $equipment_position;
			$this->shop = $shop;
		}
		
		public static function getInstance($id)
		{
		
			if(!empty(self::$instances[$id]) && self::$instances[$id] instanceof Item)
				return self::$instances[$id];
			
			$row = Db::getInstance()->query('SELECT * FROM items WHERE id = ?', $id)->getResult()->fetch_object();
			
			if(empty($row))
				return null;
			
			if($row->fk_inventory_id > 0)
				self::$instances[$id] = 
					new Container($row->id, $row->long_desc, $row->short_desc, $row->nouns, $row->value, $row->weight,
							$row->item_condition, $row->item_type, Inventory::findById($row->fk_inventory_id), $row->can_own, $row->verb,
							$row->equipment_position);
			else if(isset($row->nourishment) && $row->nourishment > 0)
				self::$instances[$id] = 
					new Food($row->id, $row->long_desc, $row->short_desc, $row->nouns, $row->value, $row->weight,
						$row->item_condition, $row->nourishment);
			else if(isset($row->thirst) && $row->thirst > 0)
				self::$instances[$id] =
					new Drink($row->id, $row->long_desc, $row->short_desc, $row->nouns, $row->value, $row->weight,
						$row->item_condition, $row->thirst);
			else						
				self::$instances[$id] = 
					new Item($row->id, $row->long_desc, $row->short_desc, $row->nouns, $row->value, $row->weight,
						$row->item_condition, $row->item_type, $row->can_own, $row->verb,
						$row->equipment_position);
			
			return self::$instances[$id];
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
						fk_inv_inside_id = ?
					WHERE
						id = ?', array($this->short, $this->long, $this->nouns, $this->value,
						$this->weight, $this->condition, $this->type, $this->can_own,
						$this->equipment_position, $this->verb, $inv_inside_id, $this->id));
			
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
					fk_inv_inside_id)
				VALUES
					(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
				array($this->short, $this->long, $this->nouns, $this->value, $this->weight,
				$this->condition, $this->type, $this->can_own, $this->equipment_position,
				$this->verb, $inv_inside_id));
			$this->id = Db::getInstance()->insert_id;
		}
		public function getShort() { return $this->short; }
		public function getLong() { return $this->long; }
		public function getNouns() { return $this->nouns; }
		public function getVerb() { return $this->verb; }
		public function getEquipmentPosition() { return $this->equipment_position; }
		public function getCanOwn() { return $this->can_own; }
		public function getValue() { return $this->value; }
		public function getId() { return $this->id; }
		public function setId($id) { $this->id = $id; }
		public function lookDescribe()
		{
			return $this->long;
		}
		public function transferOwnership(&$from, &$to)
		{
			$from->getInventory()->remove($this);
			$to->getInventory()->add($this);
		}
		
		public function copyTo($actor)
		{
			$item = $this;
			$item->setId(0);
			$item->save($actor->getInventory()->getId());
			$actor->getInventory()->add($item);
			return $item;
		}
		public function delete()
		{
			Db::getInstance()->query('DELETE FROM items WHERE id = ?', $this->id);
		}
	}

?>
