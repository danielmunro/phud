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
	class Affect
	{
	
		const GLOW = 'glow';
		const STUN = 'stun';
		
		private $affect = '';
		private $message_affect = '';
		private $message_end = '';
		private $timeout = 0;
		private $args = array();
		private $attributes = null;
		
		public function __construct()
		{
			$this->attributes = new Attributes();
		}
		
		public function getAttributes()
		{
			return $this->attributes;
		}
		
		public function setAffect($affect)
		{
			$this->affect = $affect;
		}
		
		public function getAffect()
		{
			return $this->affect;
		}
		
		public function setMessageAffect($message)
		{
			$this->message_affect = $message;
		}
		
		public function getMessageAffect()
		{
			return $this->message_affect;
		}
		
		public function setMessageEnd($message)
		{
			$this->message_end = $message;
		}
		
		public function getMessageEnd()
		{
			return $this->message_end;
		}
		
		public function setTimeout($timeout)
		{
			$this->timeout = $timeout;
		}
		
		public function getTimeout()
		{
			return $this->timeout;
		}
		
		public function setArgs($args)
		{
			$this->args = $args;
		}
		
		public function getArgs($i = '')
		{
			if($i)
				return $this->args[$i];
			else
				return $this->args;
		}
		
		public function apply(\Mechanics\Affectable $affectable)
		{
			$affectable->addAffect($this);
			if($this->timeout > 0)
				Pulse::instance()->registerEvent(
					$this->timeout,
					function($args)
					{
						$affectable = $args[0];
						$affect = $args[1];
						$affectable->removeAffect($affect);
						if($affect->getMessageEnd() && $affect instanceof \Living\User)
							Server::out($affectable, $affect->getMessageEnd());
					},
					array($affectable, $this),
					Pulse::EVENT_TICK
				);
		}
	}
?>