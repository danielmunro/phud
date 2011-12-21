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
	namespace Mechanics\Event;
	use \Mechanics\Debug;

	trait Broadcaster
	{
		protected $subscribers = [];

		public function addSubscriber(Subscriber $subscriber)
		{
			$this->subscribers[$subscriber->getType()][] = $subscriber;
		}

		public function removeSubscriber(Subscriber $subscriber)
		{
			Debug::addDebugLine('removing subscription for '.$subscriber->getSubscriber());
			$t = $subscriber->getType();
			$key = array_search($subscriber, $this->subscribers[$t]);
			unset($this->subscribers[$t][$key]);
			$this->subscribers[$t] = array_values($this->subscribers[$t]);
		}

		public function fire($event_type)
		{
			$debug = $this.' firing event for '.$event_type;
			if(!isset($this->subscribers[$event_type])) {
				$this->subscribers[$event_type] = [];
			}
			Debug::addDebugLine($debug.', '.sizeof($this->subscribers[$event_type]).' subscribers found');
			$is_received = false;
			foreach($this->subscribers[$event_type] as $i => $subscriber) {
				$callback = $subscriber->getCallback();
				$result = $callback($subscriber, $this, $subscriber->getSubscriber());
				if($subscriber->isKilled()) {
					unset($this->subscribers[$event_type][$i]);
				}
				if(!$is_received) {
					$is_received = $result;
				}
			}
			return $is_received;
		}
	}
?>
