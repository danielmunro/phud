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
	use \Items\Container;

	class Inventory
	{
		use Usable;
	
		protected $items = array();
		private static $instances = array();
		
		public function __construct()
		{
		}
		
		public function add(Item $item)
		{
			$this->items[] = $item;
		}
		
		public function addMany($items)
		{
			if(is_array($items))
				foreach($items as $i)
					$this->add($i);
		}
		
		public function remove(Item $item)
		{
			$i = array_search($item, $this->items);
			
			if($i !== false)
				unset($this->items[$i]);
			
			return $i;
		}
		
		public function getItems()
		{
			return $this->items;
		}
		
		public function getItemByInput($input)
		{
			return $this->getUsableNounByInput($this->items, $input);
		}
		
		public function getContainerByInput($input)
		{
			$container = $this->getUsableNounByInput($this->items, $input);
			return $container instanceof Container ? $container : null;
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
					$buffer .=  $pre . $key .  "\n";
				}
			}
			else
				$buffer = "Nothing.";
			return trim($buffer);
		}
		
		public function transferItemsFrom(Inventory $inventory)
		{
			$items = $inventory->getItems();
			foreach($items as $item)
			{
				$inventory->remove($item);
				$this->add($item);
			}
		}
	}
?>
