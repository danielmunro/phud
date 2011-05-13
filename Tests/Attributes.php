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
	class Attributes extends \PHPUnit_Framework_TestCase
	{
	
		private $test_id = 3294294;
		private $attributes = null;
		private $mobs = array();
	
		public function setUp()
		{
			$this->mob1 = new \Living\Mob(array(
				'race' => 'Human',
				'fk_room_id' => 1,
				//'id' => $this->test_id
			));
			$results = \Mechanics\Db::getInstance()->query('SELECT * FROM mobs')->fetch_objects();
			foreach($results as $mob)
				$this->mobs[] = new \Living\Mob($mob);
		}
	
		public function testAttributeChange()
		{
			$this->assertTrue($this->mob1->getStr() == 17);
			$this->assertTrue($this->mob1->getAttributes()->getStr() == 17);
			$this->mob1->setStr(18);
			$this->assertTrue($this->mob1->getStr() == 18);
			$this->assertTrue($this->mob1->getAttributes()->getStr() == 18);
		}
		
		public function testUserAttributes()
		{
			$user = new \Living\User(null);
			$user->loadByAliasAndPassword('dan', 'qwerty');
			$this->assertTrue($user->getAttributes()->getStr() > 0);
		}
		
		public function testAllMobs()
		{
			foreach($this->mobs as $mob)
			{
				$this->assertTrue($mob->getAttributes() instanceof \Mechanics\Attributes);
			}
		}
	}
?>
