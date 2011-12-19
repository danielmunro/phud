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
	namespace Mechanics\Command;
	use \Living\User,
		\Mechanics\Debug,
		\Mechanics\Server,
		\Mechanics\Performable,
		\ReflectionClass,
		\Mechanics\Actor;

	abstract class Command implements Performable
	{
		protected $dispositions = array();
		
		protected function __construct() {}
		
		public function runInstantiation()
		{
			$namespace = 'Commands';
			$d = dir(dirname(__FILE__) . '/../../'.$namespace);
			while($command = $d->read())
				if(substr($command, -4) === ".php")
				{
					Debug::addDebugLine("init command: ".$command);
					$class = substr($command, 0, strpos($command, '.'));
					$called_class = $namespace.'\\'.$class;
					$reflection = new ReflectionClass($called_class);
					if(!$reflection->isAbstract()) {
						new $called_class();
					}
				}
		}
	
		public function getDispositions()
		{
			return $this->dispositions;
		}
		
		public function hasArgCount(Actor $actor, $args, $count)
		{
			if(sizeof($args) < $count)
			{
				Server::out($actor, "Not enough args.");
				return false;
			}
			return true;
		}
	
		public function tryPerform(User $user, $args = [])
		{
			if($this instanceof DM && !$user->isDM())
				return Server::out($user, "You cannot do that.");
			else if($user->getDisposition() === Actor::DISPOSITION_SITTING)
				return Server::out($user, "You need to stand up.");
			else if($user->getDisposition() === Actor::DISPOSITION_SLEEPING)
				return Server::out($user, "You are asleep!");
			
			$this->perform($user, $args);
		}
	}
?>
