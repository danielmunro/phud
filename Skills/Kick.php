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
	namespace Skills;
	class Kick extends \Mechanics\Skill
	{
	
		protected $creation_points = 5;
	
		protected function __construct()
		{
			$this->alias = new \Mechanics\Alias('kick', $this);
			$this->base_class = \Disciplines\Warrior::instance();
			parent::__construct();
		}
	
		public function perform(\Mechanics\Actor $actor, $chance = 0, $args = array())
		{
			
			$actor_target = $actor->getTarget();
			$specified_target = $actor->getRoom()->getActorByInput($args);
			
			$final_target = null;
			
			if($actor_target instanceof \Mechanics\Actor)
				$final_target = $actor_target;
			else if($specified_target instanceof \Mechanics\Actor)
				$final_target = $specified_target;
			
			if($final_target === null)
				return \Mechanics\Server::out($actor, 'You kick your legs wildly!');
			
			$actor->incrementDelay(1);
			$base_chance = 99;
			
			if(!($actor_target instanceof \Mechanics\Actor))
				$actor->addFighter($final_target);

			if($chance > $base_chance || $chance > $this->getPercent())
				return \Mechanics\Server::out($actor, 'You fall flat on your face!');
			
			if($actor->damage($final_target, rand(1, 1 + $actor->getLevel()), \Mechanics\Damage::TYPE_BASH))
			{
				\Mechanics\Server::out($actor, 'You kick ' . $final_target->getAlias() . ', causing him pain!');
				\Mechanics\Server::out($final_target, $actor->getAlias(true) . ' kicks you!');
			}
		}
	}

?>
