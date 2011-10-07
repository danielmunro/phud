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
	abstract class Equipment extends \Mechanics\Item
	{
		const POSITION_LIGHT = 'light';
		const POSITION_FINGER = 'finger';
		const POSITION_NECK = 'neck';
		const POSITION_BODY = 'body';
		const POSITION_HEAD = 'head';
		const POSITION_LEGS = 'legs';
		const POSITION_FEET = 'feet';
		const POSITION_HANDS = 'hands';
		const POSITION_ARMS = 'arms';
		const POSITION_TORSO = 'torso';
		const POSITION_WAIST = 'waist';
		const POSITION_WRIST = 'wrist';
		const POSITION_HOLD = 'hold';
		const POSITION_FLOAT = 'float';
		const POSITION_WIELD = 'wield';
		const POSITION_GENERIC = 'generic';
	
		protected $position = 0;
		protected $condition = 100;
		protected $size = 0;
		
		public function __construct()
		{
			parent::__construct();
		}
		
		public function getPosition()
		{
			return $this->position;
		}
		
		public function setPosition($position)
		{
			$this->position = $position;
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
		
		public static function getPositionByStr($position)
		{
			switch(strtolower($position))
			{
				case strpos('light', $position) === 0:
					return self::POSITION_LIGHT;
				case strpos('finger', $position) === 0:
					return self::POSITION_FINGER;
				case strpos('neck', $position) === 0:
					return self::POSITION_NECK;
				case strpos('body', $position) === 0:
					return self::POSITION_BODY;
				case strpos('head', $position) === 0:
					return self::POSITION_HEAD;
				case strpos('legs', $position) === 0:
					return self::POSITION_LEGS;
				case strpos('feet', $position) === 0:
					return self::POSITION_FEET;
				case strpos('hands', $position) === 0:
					return self::POSITION_HANDS;
				case strpos('arms', $position) === 0:
					return self::POSITION_ARMS;
				case strpos('torso', $position) === 0:
					return self::POSITION_TORSO;
				case strpos('waist', $position) === 0:
					return self::POSITION_WAIST;
				case strpos('wrist', $position) === 0:
					return self::POSITION_WRIST;
				case strpos('hold', $position) === 0:
					return self::POSITION_HOLD;
				case strpos('float', $position) === 0:
					return self::POSITION_FLOAT;
				case strpos('wield', $position) === 0:
					return self::POSITION_WIELD;
				default:
					return false;
			}
		}
		
		public function getInformation()
		{
			return 
				"===========================\n".
				"== Equipment Information ==\n".
				"===========================\n".
				"position:              ".Equipped::getLabelByPosition($this->position)."\n".
				"condition:             ".$this->getCondition()."\n".
				"size:                  ".$this->getSize().
				parent::getInformation();
		}
	}

?>
