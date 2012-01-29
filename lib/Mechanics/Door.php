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
	class Door
	{
		private $id = 0;
		private $short = 'a door';
		private $long = 'a generic door is here.';
		private $key_unlock = null;
		private $disposition = self::DISPOSITION_CLOSED;
		private $default_disposition = self::DISPOSITION_CLOSED;
		private $nouns = 'door';
		private $is_hidden = false;
		private $default_is_hidden = false;
		private $hidden_show_command = '';
		private $hidden_action = '';
		private $hidden_item_reveal = null;
		private $reload_ticks = 5;
		private $partner_door = null;
	
		const DISPOSITION_LOCKED = 'locked';
		const DISPOSITION_OPEN = 'open';
		const DISPOSITION_CLOSED = 'closed';
		
		public function __construct()
		{
		}
		
		public function decreaseReloadTick()
		{
			if($this->disposition != $this->default_disposition)
				$this->reload_ticks--;
			return $this->reload_ticks;
		}
		
		public function reload()
		{
			$this->disposition = $this->default_disposition;
			$this->is_hidden = $this->default_is_hidden;
		}
		
		public function getParnterDoor()
		{
			return $this->partner_door;
		}
		
		public function setPartnerDoor(Door $door)
		{
			$this->partner_door = $door;
		}
		
		public function getDisposition()
		{
			return $this->disposition;
		}
		
		public function setDisposition($disposition)
		{
			$this->disposition = $disposition;
		}
		
		public function getDefaultDisposition()
		{
			return $this->default_disposition;
		}
		
		public function setDefaultDisposition($default_disposition)
		{
			$this->default_disposition = $default_disposition;
		}
		
		public function getShort()
		{
			return $this->short;
		}
		
		public function getLong()
		{
			return $this->long;
		}
		
		public function getNouns()
		{
			return $this->nouns;
		}
		
		public function isHidden()
		{
			return $this->is_hidden;
		}
		
		public function setIsHidden($is_hidden)
		{
			$this->is_hidden = $is_hidden;
		}
		
		public function isDefaultHidden()
		{
			return $this->default_is_hidden;
		}
		
		public function setIsDefaultHidden($default_is_hidden)
		{
			$this->default_is_hidden = $default_is_hidden;
		}
		
		public function __toString()
		{
			return $this->short;
		}
	}
?>
