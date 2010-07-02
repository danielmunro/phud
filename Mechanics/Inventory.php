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
	class Inventory
	{
	
		protected $id = 0;
		protected $table_id = 0;
		protected $table = '';
		protected $items = array();
		
		private static $instances = array();
		
		public function __construct($table = '', $table_id = 0)
		{
			$this->table = $table;
			$this->table_id = $table_id;
		}
		
		public static function findById($id)
		{
			
			$row = Db::getInstance()->query(
				'SELECT * FROM inventories WHERE id = ?', $id)->getResult()->fetch_object();
			
			return self::find($row->fk_table, $row->fk_table_id);
		}
		
		public static function find($table, $table_id)
		{
			
			if(!empty(self::$instances[$table][$table_id]) && self::$instances[$table][$table_id] instanceof Inventory)
				return self::$instances[$table][$table_id];
			
			$rows = Db::getInstance()->query('
				SELECT
					inventories.id AS inventory_id,
					items.id AS item_id,
					items.fk_inventory_id
				FROM inventories
				LEFT JOIN items ON items.fk_inv_inside_id = inventories.id
				WHERE fk_table = ? AND fk_table_id = ?',
					array($table, $table_id))->fetch_objects();

			// NEW....
			if(empty($rows))
			{
				self::$instances[$table][$table_id] = new self($table, $table_id);
				return self::$instances[$table][$table_id];
			}
			
			self::$instances[$table][$table_id] = new self($table, $table_id);
			
			self::$instances[$table][$table_id]->setId($rows[0]->inventory_id);
			
			foreach($rows as $row)
			{
				if($row->item_id == 0)
					continue;
			
				self::$instances[$table][$table_id]->add(\Items\Item::getInstance($row->item_id));
			}
			return self::$instances[$table][$table_id];
		}
		
		public function add(\Items\Item $item)
		{
			$this->items[] = $item;
		}
		
		public function remove(\Items\Item $item, $hard = false)
		{
			foreach($this->items as $key => $i)
				if($i->getShort() == $item->getShort())
				{
					unset($this->items[$key]);
					if($hard)
						$item->delete();
					return;
				}
		}
		
		public function getItems() { return $this->items; }
		
		public function getItemByInput($input)
		{
		
			foreach($this->items as $item)
			{
				$nouns = $item->getNouns();
				if(!is_array($nouns))
					$nouns = explode(' ', $nouns);
				foreach($nouns as $noun)
					if(strpos($noun, $input[1]) === 0)
						return $item;
			}
		
		}
		
		public function getContainerByInput($input)
		{
			
			foreach($this->items as $item)
			{
				$nouns = $item->getNouns();
				if(!is_array($nouns))
					$nouns = explode(' ', $nouns);
				foreach($nouns as $noun)
					if(strpos($noun, $input[1]) === 0 && $item instanceof \Items\Container)
						return $item;
			}
		}
		
		public function displayContents($show_prices = false)
		{
		
			$buffer = '';
			if(sizeof($this->items) > 0)
			{
				$items = array();
				$prices = array();
				
				foreach($this->items as $item)
				{
					if(!isset($items[$item->getShort()]))
						$items[$item->getShort()] = 0;
					$items[$item->getShort()] += 1;
					$prices[$item->getShort()] = $item->getValue();
				}
				foreach($items as $key => $item)
				{
					if($show_prices)
						$pre = $prices[$key] . ' copper - ';
					else
						$pre = ($item > 1 ? '(' . $item . ') ' : '' );
					$buffer .=  $pre . ucfirst($key);
					if(sizeof($items) - 1 < $key)
						$buffer .= "\n";
				}
			}
			else
				$buffer = "Nothing.";
			return $buffer;
		}
		
		public function save()
		{
			
			if(!sizeof($this->items))
				return;
			
			if($this->id)
				Db::getInstance()->query('UPDATE inventories SET fk_table = ?, fk_table_id = ?
					WHERE id = ?', array($this->table, $this->table_id, $this->id));
			else
			{
				Db::getInstance()->query('INSERT INTO inventories (fk_table, fk_table_id) VALUES
					(?, ?)', array($this->table, $this->table_id));
				$this->id = Db::getInstance()->insert_id;
			}
			foreach($this->items as $i => $item)
			{
				$item->save($this->id);
				if($item instanceof Container)
					$item->getInventory()->save($this->id);
			}
		}
		
		public function setId($id) { $this->id = $id; }
		public function getId() { return $this->id; }
		public function setTable($table) { $this->table = $table; }
		public function setTableId($id) { $this->table_id = $id; }
	}

?>
