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
	namespace Commands;
	use \Mechanics\Actor,
		\Mechanics\Alias,
		\Mechanics\Server,
		\Mechanics\Event\Subscriber,
		\Mechanics\Event\Event,
		\Mechanics\Command\Command;

	class Kill extends Command
	{
		protected $dispositions = array(Actor::DISPOSITION_STANDING);
	
		protected function __construct()
		{
			self::addAlias('kill', $this);
		}
	
		public function perform(Actor $actor, $args = [], Subscriber $command_subscriber)
		{
			if(!$actor->reconcileTarget($args)) {
				return;
			}

			$actor->getTarget()->fire(Event::EVENT_ATTACKED, $actor, $command_subscriber);
			if(!$command_subscriber->isSuppressed()) {
				Server::out($actor, "You scream and attack!");
				Server::instance()->addSubscriber($actor->getAttackSubscriber());
			}
		}
	}
?>
