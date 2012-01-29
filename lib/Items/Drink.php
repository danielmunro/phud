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
	use \Mechanics\Item,
		\Mechanics\Server,
		\Mechanics\Actor;

	class Drink extends Item
	{
		protected $short = 'a generic drink container';
		protected $long = 'A generic drink container lays here';
		protected $nouns = 'generic drink container';
		protected $amount = 0;
		protected $contents = '';
		protected $thirst = 0;
		protected $uses = 0;
		
		public function getAmount()
		{
			return $this->amount;
		}
		
		public function setAmount($amount)
		{
			$this->amount = $amount;
			$this->uses = $amount;
		}
		
		public function drink(Actor $actor)
		{
			if($this->uses === 0)
			{
				Server::out($actor, "There's no ".$contents." left.");
				return false;
			}
			
			if($actor->increaseThirst($this->thirst)) {
				$this->uses--;
				return true;
			}
		}
		
		private function fill()
		{
			$this->uses = $this->amount;
		}
		
		public function getContents()
		{
			return $this->contents;
		}
		
		public function setContents($contents)
		{
			$this->contents = $contents;
			$this->fill();
		}

		public function setThirst($thirst)
		{
			$this->thirst = $thirst;
		}

		public function getThirst()
		{
			return $this->thirst;
		}
		
		public function getInformation()
		{
			return
				"==Drink Attributes==\n".
				"====================\n".
				"thirst:             ".$this->getThirst()."\n".
				"amount:             ".$this->getAmount()."\n".
				"contents:           ".$this->getContents()."\n".
				parent::getInformation();
		}
	}

?>
