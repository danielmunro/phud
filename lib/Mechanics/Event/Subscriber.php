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

	class Subscriber
	{
		const TYPE_ACTOR_MOVED = 1;
		const TYPE_USER_INPUT = 2;

		protected $type = 0;
		protected $subscriber = null;
		protected $condition = null;
		protected $callback = null;

		public function __construct($type, $subscriber, $condition, $callback = null)
		{
			$this->type = $type;
			$this->subscriber = $subscriber;
			Debug::addDebugLine('new subscriber: '.$subscriber.', '.$type);
			if(is_null($callback)) {
				Debug::addDebugLine('empty callback');
				$callback = $condition;
				$condition = function() {
					Debug::addDebugLine('Standard condition');
					return true;
				};
			}
			$this->condition = $condition;
			$this->callback = $callback;
		}

		public function getType()
		{
			return $this->type;
		}

		public function getSubscriber()
		{
			return $this->subscriber;
		}
		
		public function getCondition()
		{
			return $this->condition;
		}

		public function getCallback()
		{
			return $this->callback;
		}
	}
?>
