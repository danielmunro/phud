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
	use \Mechanics\Actor;
	use \Mechanics\Dbr;
	class Shopkeeper extends Mob
	{
	
		protected $alias = 'a shopkeeper';
		protected $long = 'a shopkeeper stands here.';
		protected $nouns = 'shopkeeper';
		protected $list_item_message = "Here's what I have in stock now.";
		protected $no_item_message = "I'm not selling that.";
		protected $not_enough_money_message = "Come back when you have more money.";
		
		public function setListItemMessage($message)
		{
			$this->list_item_message = $message;
		}
		
		public function getListItemMessage()
		{
			return $this->list_item_message;
		}
		
		public function setNoItemMessage($message)
		{
			$this->no_item_message = $message;
		}
		
		public function getNoItemMessage()
		{
			return $this->no_item_message;
		}
		
		public function setNotEnoughMoneyMessage($message)
		{
			$this->not_enough_money_message = $message;
		}
		
		public function getNotEnoughMoneyMessage()
		{
			return $this->not_enough_money_message;
		}
	}
?>
