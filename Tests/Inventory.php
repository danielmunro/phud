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
	namespace Tests;
	class Inventory extends \PHPUnit_Framework_TestCase
	{
	
		private $mob1 = null;
		private $mob2 = null;
	
		public function setUp()
		{
			\Mechanics\Debug::newLog();
			/**
			\Mechanics\Command::runInstantiation();
			\Living\Mob::instantiate();
			\Living\Shopkeeper::instantiate();
			$this->user = new \Living\User(null);
			$this->user->handleLogin('dan');
			$this->user->handleLogin('qwerty');
			*/
			$this->mob1 = new \Living\Mob(array(
				'race' => 'Human',
				'fk_room_id' => 1,
				'id' => 3294294 // rand -- we don't want an id match
			));
			$this->mob2 = new \Living\Mob(array(
				'race' => 'Human',
				'fk_room_id' => 1,
				'id' => 304203242 // rand -- we don't want an id match
			));
		}
	
		public function testInventoryExists()
		{
			$i = $this->mob1->getInventory();
			$this->assertTrue($i instanceof \Mechanics\Inventory);
			$this->assertTrue(sizeof($i->getItems()) === 0);
			
			$i = $this->mob2->getInventory();
			$this->assertTrue($i instanceof \Mechanics\Inventory);
			$this->assertTrue(sizeof($i->getItems()) === 0);
		}
		
		public function testAddItem()
		{
			$item = \Items\Item::getInstance(1);
			$item->copyTo($this->mob1);
			
			$i = $this->mob1->getInventory();
			$this->assertTrue(sizeof($i->getItems()) == 1);
			$this->assertTrue(array_shift($i->getItems())->getShort() == $item->getShort());
		}
	}
?>
