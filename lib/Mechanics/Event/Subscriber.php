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
	use \Mechanics\Debug,
		\Closure;

	class Subscriber
	{
		protected $event_type = '';
		protected $subscriber = null;
		protected $callback = null;
		protected $killed = false;
		protected $suppressed = false;
		protected $broadcast_satisfied = false;

		public function __construct($event_type, $subscriber, $callback = null, $deferred = false)
		{
			// method overloading would be nice
			$this->event_type = $event_type;
			if($callback instanceof Closure) {
				$this->subscriber = $subscriber;
				$this->callback = $callback;
				$this->deferred = $deferred;
			} else {
				$this->subscriber = null;
				$this->callback = $subscriber;
				$this->deferred = $callback;
			}
		}

		public function getEventType()
		{
			return $this->event_type;
		}

		public function getSubscriber()
		{
			return $this->subscriber;
		}

		public function getCallback()
		{
			return $this->callback;
		}

		public function isDeferred()
		{
			return $this->deferred;
		}

		public function kill()
		{
			$this->killed = true;
		}

		public function isKilled()
		{
			return $this->killed;
		}

		public function satisfyBroadcast($satisfied = true)
		{
			$this->broadcast_satisfied = $satisfied;
		}

		public function isBroadcastSatisfied()
		{
			return $this->broadcast_satisfied;
		}

		public function suppress()
		{
			$this->suppressed = true;
		}

		public function isSuppressed()
		{
			return $this->suppressed;
		}
	}
?>
