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
	namespace Living;
	class Shopkeeper extends \Mechanics\Actor
	{
	
		protected $start_room_id = 0;
		protected $noun = '';
		private $list_item_message = "Here's what I have in stock now";
		private $no_item_message = "I'm not selling that";
		private $not_enough_money_message = "Come back when you have more money";
	
		public function __construct($properties)
		{
			
			foreach($properties as $property => $value)
			{
				if($property == 'race')
					$this->setRace($value);
				elseif($property == 'fk_room_id')
					$this->start_room_id = $value;
				elseif(property_exists($this, $property))
					$this->$property = $value;
			}
			parent::__construct($this->start_room_id);
		}
		
		public static function instantiate($data = null)
		{
			\Mechanics\Debug::addDebugLine('Initializing shopkeepers');
			$results = \Mechanics\Db::getInstance()->query('SELECT * FROM shopkeepers')->fetch_objects();
			\Mechanics\Debug::addDebugLine('shopkeepers: '.sizeof($results));
			foreach($results as $shopkeeper)
				new self($shopkeeper);
		}
		
		public function save()
		{
			\Mechanics\Debug::addDebugLine("SAVING SHOPKEEPER");
			if($this->id)
			{
				\Mechanics\Db::getInstance()->query('UPDATE shopkeepers SET alias = ?, noun = ?, `long` = ?, gold = ?, silver = ?, copper = ?, race = ?, 
					fk_room_id = ?, level = ?, list_item_message = ?, no_item_message = ?, not_enough_money_message = ? WHERE id = ?', array ($this->alias, $this->noun,
					$this->long, $this->gold, $this->silver, $this->copper, $this->race, $this->room->getId(),$this->level, $this->list_item_message,
					$this->no_item_message, $this->not_enough_money_message, $this->id), true);
			}
			else
			{
				\Mechanics\Db::getInstance()->query('INSERT INTO shopkeepers (alias, noun, `long`, gold, silver, copper, race, fk_room_id, level, list_item_message,
					no_item_message, not_enough_money_message) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array ($this->alias, $this->noun, $this->long, 
					$this->gold, $this->silver, $this->copper, $this->race, $this->room->getId(), $this->level, $this->list_item_message, $this->no_item_message,
					$this->not_enough_money_message), true);
				$this->id = \Mechanics\Db::getInstance()->insert_id;
				$this->inventory->setTableId($this->id);
			}
			$this->inventory->save();
			$this->ability_set->save();
		}
		
		public function tick() {}
		
		public function setListItemMessage($message) { $this->list_item_message = $message; }
		public function getListItemMessage() { return $this->list_item_message; }
		
		public function setNoItemMessage($message) { $this->no_item_message = $message; }
		public function getNoItemMessage() { return $this->no_item_message; }
		
		public function setNotEnoughMoneyMessage($message) { $this->not_enough_money_message = $message; }
		public function getNotEnoughMoneyMessage() { return $this->not_enough_money_message; }
		
		public function getTable()
		{
			return 'shop';
		}
		protected function levelUp() {}
	}

?>
