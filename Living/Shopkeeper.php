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

	class Shopkeeper extends Actor
	{
	
		private $list_item_message = "Here's what I have in stock now";
		private $no_item_message = "I'm not selling that";
		private $not_enough_money_message = "Come back when you have more money";
	
		public function __construct($alias, $noun, $description, $area, $room_id, $level, $race)
		{
			
			$this->alias = $alias;
			$this->noun = $noun;
			$this->description = $description;
			$this->area = $area;
			$this->level = $level;
			$this->setRace($race);
			$this->fightable = false;
			
			parent::__construct($room_id);
		}
		
		public function setListItemMessage($message) { $this->list_item_message = $message; }
		public function getListItemMessage() { return $this->list_item_lessage; }
		
		public function setNoItemMessage($message) { $this->no_item_message = $message; }
		public function getNoItemMessage() { return $this->no_item_message; }
		
		public function setNotEnoughMoneyMessage($message) { $this->not_enough_money_message = $message; }
		public function getNotEnoughMoneyMessage() { return $this->not_enough_money_message; }
		
		public function getTable()
		{
			return 'shop';
		}
	}

?>
