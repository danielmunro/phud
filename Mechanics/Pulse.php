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
		const EVENT_PULSE = 1;
		const EVENT_TICK = 2;
		
		private function __construct()
		{
			$this->seconds = $this->tick = date('U');
			$this->events = array(self::EVENT_PULSE => array(), self::EVENT_TICK => array());
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
			$this->checkTickEvents();
			self::registerEvent($seconds, function() { Pulse::instance()->tick(); }, null);
		}
		
		public static function getRandomSeconds($seconds, $mod = 0.1)
		{
			$modifier = $mod * $seconds;
			$low_mod = $seconds - $modifier;
			$high_mod = $seconds + $modifier;
			return rand($low_mod, $high_mod);
		}
		
		public function checkTickEvents()
		{
			$events = array_shift($this->events[self::EVENT_TICK]);
			if($events)
			{
				foreach($events as $event)
					$event['fn']($event['args']);
			}
		}
		
		public function checkPulseEvents($seconds)
		{
			$this->seconds = $seconds;
			$this->next_tick--;
			if(isset($this->events[self::EVENT_PULSE][$this->seconds]))
			{
				foreach($this->events[self::EVENT_PULSE][$this->seconds] as $event)
					$event['fn']($event['args']);
				unset($this->events[self::EVENT_PULSE][$this->seconds]);
			}
		}
		
		public function registerNextTickEvent($fn, $args)
		{
			$this->registerEvent($this->next_tick, $fn, $args);
		}
		
		public function registerEvent($duration, $fn, $args, $type = self::EVENT_PULSE)
		{
			Debug::addDebugLine('Registering event');
		
			if($type === self::EVENT_PULSE)
				$duration = $this->seconds + $duration;
			
			if(!isset($this->events[$type][$duration]))
				$this->events[$type][$duration] = array();
			
			$this->events[$type][$duration][] = array('fn' => $fn, 'args' => $args);
			return sizeof($this->events[$type][$duration]);
		}
		
		public function getLastPulse()
		{
			return $this->seconds;
		}
	}
?>
