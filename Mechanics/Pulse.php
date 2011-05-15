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
	class Pulse
	{
		private $tick = 0;
		private $next_tick = 0;
		private $pick = 0;
		private $events = array();
		private $seconds = 0;
		private static $instance = null;
		
		const SECONDS_PER_TICK = 30;
		
		private function __construct()
		{
			$this->seconds = $this->tick = date('U');
			$this->tick();
		}
		
		public static function instance()
		{
			if(!isset(self::$instance))
				self::$instance = new self();
			return self::$instance;
		}
		
		public function tick()
		{
			Debug::addDebugLine("Tick starting at " . date('Y-m-d H:i:s'));
			$seconds = self::getRandomSeconds(self::SECONDS_PER_TICK);
			$this->tick = date('U');
			$this->next_tick = $seconds;
			self::registerEvent($seconds, function() { Pulse::instance()->tick(); }, null);
		}
		
		public static function getRandomSeconds($seconds, $mod = 0.1)
		{
			$modifier = $mod * $seconds;
			$low_mod = $seconds - $modifier;
			$high_mod = $seconds + $modifier;
			return rand($low_mod, $high_mod);
		}
		
		public function checkEvents($seconds)
		{
			$this->seconds = $seconds;
			$this->next_tick--;
			// Cycle through events
			if(isset($this->events[$this->seconds]))
			{
				foreach($this->events[$this->seconds] as $event)
					$event['fn']($event['args']);
				unset($this->events[$this->seconds]);
			}
		}
		
		public function registerTickEvent($fn, $args)
		{
			$this->registerEvent($this->next_tick, $fn, $args);
		}
		
		public function registerEvent($seconds, $fn, $args)
		{
			$seconds = $this->seconds + $seconds;
			Debug::addDebugLine('Registering event ('.$seconds.')');
			if(!isset($this->events[$seconds]))
				$this->events[$seconds] = array();
			$this->events[$seconds][] = array('fn' => $fn, 'args' => $args);
			return sizeof($this->events[$seconds]);
		}
		
		public function getLastPulse()
		{
			return $this->seconds;
		}
	}
?>
