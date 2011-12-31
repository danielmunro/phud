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
		protected $deferred_subscribers = [];

		public function addSubscriber(Subscriber $subscriber)
		{
			if($subscriber->isDeferred()) {
				$this->deferred_subscribers[$subscriber->getEventType()][] = $subscriber;
			} else {
				$this->subscribers[$subscriber->getEventType()][] = $subscriber;
			}
		}

		public function removeSubscriber(Subscriber $subscriber)
		{
			$t = $subscriber->getEventType();
			$key = array_search($subscriber, $this->subscribers[$t]);
			if($key !== false) {
				unset($this->subscribers[$t][$key]);
			}
			$key = array_search($subscriber, $this->deferred_subscribers[$t]);
			if($key !== false) {
				unset($this->deferred_subscribers[$t][$key]);
			}
		}

		public function fire($event_type, &$a1 = null, &$a2 = null, &$a3 = null, &$a4 = null)
		{
			if(!isset($this->subscribers[$event_type])) {
				$this->subscribers[$event_type] = [];
			}
			if(!isset($this->deferred_subscribers[$event_type])) {
				$this->deferred_subscribers[$event_type] = [];
			}
			$is_satisfied = $this->_fire($this->subscribers[$event_type], $a1, $a2, $a3, $a4);
			$this->_fire($this->deferred_subscribers[$event_type], $a1, $a2, $a3, $a4);
			return $is_satisfied;
		}

		private function _fire($subscribers, &$a1, &$a2, &$a3, &$a4)
		{
			foreach($subscribers as $i => $subscriber) {
				$callback = $subscriber->getCallback();
				if($subscriber->getSubscriber()) {
					$callback($subscriber, $this, $subscriber->getSubscriber(), $a1, $a2, $a3, $a4);
				} else {
					$callback($subscriber, $this, $a1, $a2, $a3, $a4);
				}
				if($subscriber->isKilled()) {
					unset($this->subscribers[$subscriber->getEventType()][$i]);
					continue;
				}
				$is_satisfied = $subscriber->isBroadcastSatisfied();
				if($is_satisfied) {
					$subscriber->satisfyBroadcast(false);
					return $is_satisfied;
				}
			}
		}
	}
?>
