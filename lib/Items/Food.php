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
	use \Mechanics\Item;
	class Food extends Item
	{
		protected $short = 'a generic food item';
		protected $long = 'A generic food item lays here';
		protected $nouns = 'generic food';
		protected $nourishment = 0;

		public function __construct()
		{
			parent::__construct();
		}
		
		public function getNourishment()
		{
			return $this->nourishment;
		}
		
		public function setNourishment($nourishment)
		{
			$this->nourishment = $nourishment;
		}
		
		public function getInformation()
		{
			return
				"===================\n".
				"==Food Attributes==\n".
				"===================\n".
				"nourishment:       ".$this->nourishment."\n".
				parent::getInformation();
		}
	}

?>