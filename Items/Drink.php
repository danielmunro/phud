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
	class Drink extends \Mechanics\Item
	{
		protected $short = 'a generic drink container';
		protected $long = 'A generic drink container lays here';
		protected $nouns = 'generic drink container';
		protected $size = 0;
		protected $amount = 0;
		protected $contents = '';
		protected $thirst = 0;
		protected $nourishment = 0;

		public function __construct($size = 5)
		{
			$this->size = $size;
			parent::__construct();
		}
		
		public function getSize()
		{
			return $this->size;
		}
		
		public function setSize($size)
		{
			$this->size = $size;
		}
		
		public function getAmount()
		{
			return $this->amount;
		}
		
		public function use(\Mechanics\Actor $actor)
		{
			if(!$this->amount)
			{
				\Mechanics\Server::out($actor, "There's no ".$contents." left.");
				return false;
			}
			
			if($this->nourishment && $actor->isNourishmentFull())
			{
				\Mechanics\Server::out($actor, "You are too full to drink more ".$contents.".");
				return false;
			}
			
			if($this->thirst && $actor->isThirstFull())
			{
				\Mechanics\Server::out($actor, "Your thirst has been quenched.");
				return false;
			}
			
			$this->amount--;
			$actor->increaseNourishment($this->nourishment);
			$actor->increaseThirst($this->thirst);
			return true;
		}
		
		private function fill()
		{
			$this->amount = $this->size;
		}
		
		public function getContents()
		{
			return $this->contents;
		}
		
		public function setContents($contents, $thirst, $nourishment)
		{
			$this->contents = $contents;
			$this->thirst = $thirst;
			$this->nourishment = $nourishment;
			$this->fill();
		}
	}

?>
