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
	abstract class Equipment extends \Mechanics\Item
	{
		const POSITION_LIGHT = 0;
		const POSITION_FINGER = 1;
		const POSITION_NECK = 2;
		const POSITION_BODY = 3;
		const POSITION_HEAD = 4;
		const POSITION_LEGS = 5;
		const POSITION_FEET = 6;
		const POSITION_HANDS = 7;
		const POSITION_ARMS = 8;
		const POSITION_TORSO = 9;
		const POSITION_WAIST = 10;
		const POSITION_WRIST = 11;
		const POSITION_HOLD = 13;
		const POSITION_FLOAT = 14;
		const POSITION_WIELD = 15;
		const POSITION_GENERIC = 16;
	
		protected $condition = 100;
		protected $size = 0;
		
		public function __construct()
		{
			parent::__construct();
		}
		
		public function getCondition()
		{
			return $this->condition;
		}
		
		public function setCondition($condition)
		{
			$this->condition = $condition;
		}
		
		public function addCondition($condition)
		{
			$this->condition += $condition;
		}
		
		public function getSize()
		{
			return $this->size;
		}
		
		public function setSize($size)
		{
			$this->size = $size;
		}
	}

?>
