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
	class Event
	{
		const TYPE_ACTOR_MOVED = 1;
		const TYPE_USER_INPUT = 2;

		protected static $instance = null;
		protected $events = [];

		protected function __construct() {}

		protected static function instance()
		{
			if(!self::$instance)
				self::$instance = new self();
			return self::$instance;
		}

		public static function fire($event_type, $listener)
		{
			self::instance()->_fire($event_type, $listener);
		}

		public function _fire($event_type, $listener)
		{
			if(is_array($this->events[$event_type][$listener])) {
				foreach($this->events[$event_type][$listener] as $subscriber => $event) {
					if($event[0]($listener, $subscriber)) {
						$event[1]($listener, $subscriber);
					}
				}
			}
		}

		public static function subscribe($event_type, $listener, $subscriber, $f1, $f2 = null)
		{
			self::instance()->_subscribe($event_type, $listener, $subscriber, $f1, $f2);
		}

		public function _subscribe($event_type, $listener, $subscriber, $f1, $f2)
		{
			if(is_null($f2)) {
				$condition = function() { return true; };
				$callback = $f1;
			} else {
				$condition = $f1;
				$callback = $f2;
			}
			$this->events[$event_type][$listener][$subscriber] = [$condition, $callback];
		}

		public static function unsubscribe($event_type, $listener, $subscriber)
		{
			self::instance()->_unsubscribe($event_type, $listener, $subscriber);
		}

		public function _unsubscribe($event_type, $listener, $subscriber)
		{
			unset($this->events[$event_type][$listener][$subscriber]);
		}
	}
?>
