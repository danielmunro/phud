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
	use \Mechanics\Server;
	use \Mechanics\Actor;
	class Drink extends Item
	{
		protected $short = 'a generic drink container';
		protected $long = 'A generic drink container lays here';
		protected $nouns = 'generic drink container';
		protected $amount = 0;
		protected $contents = '';
		protected $thirst = 0;
		protected $uses = 0;
		
		public function getUses()
		{
			return $this->uses;
		}
		
		public function setUses($uses)
		{
			$this->uses = $uses;
		}
		
		public function getAmount()
		{
			return $this->amount;
		}
		
		public function setAmount($amount)
		{
			$this->amount = $amount;
		}
		
		public function use(Actor $actor)
		{
			if(!$this->amount)
			{
				Server::out($actor, "There's no ".$contents." left.");
				return false;
			}
			
			if($this->thirst && $actor->isThirstFull())
			{
				Server::out($actor, "Your thirst has been quenched.");
				return false;
			}
			
			$this->amount--;
			$actor->increaseThirst($this->thirst);
			return true;
		}
		
		private function fill()
		{
			$this->uses = $this->amount;
		}
		
		public function getContents()
		{
			return $this->contents;
		}
		
		public function setContents($contents, $thirst)
		{
			$this->contents = $contents;
			$this->thirst = $thirst;
			$this->fill();
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
