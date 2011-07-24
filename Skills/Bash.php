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
	class Bash extends \Mechanics\Skill
	{
	
		protected $aliases = array('bash', 'b', 'ba', 'bas');
		protected $creation_points = 5;
		protected $fail_message = 'You fall flat on your face!';
	
		protected function __construct()
		{
			$this->base_class = \Disciplines\Warrior::instance();
			parent::__construct();
		}
	
		public function perform(\Mechanics\Actor &$actor, $args = null)
		{
			$target_args = $actor->getRoom()->getActorByInput($args);
			$target_fighting = $actor->getTarget();
			
			if($target_fighting && $target_args && $target_fighting != $target_args)
					return "Whoa! Don't you think one is enough!";
			
			$target = $target_fighting ? $target_fighting : $target_args;
			
			if(!$target)
				return \Mechanics\Server::out($actor, "You bash around, all to yourself!");
			
			$chance = rand(0, 100);
			
			$actor_mod = 5 - $actor->getRace()->getSize();
			$target_mod = 5 - $target->getRace()->getSize();
			
			$actor->incrementDelay(2);
			
			if($chance / $target_mod < $this->getPercent() / $actor_mod)
			{
				$a = new Affect();
				$a->setAffect('stun');
				$a->setTimeout(0);
				$target->addAffect($a);
				return "You slam into " . $target->getAlias() . " and send " . $target->getSex() . " flying!";
			}
			
			return $this->fail_message;
		}
	}
?>
