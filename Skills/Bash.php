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
	
		protected $creation_points = 5;
		protected $fail_message = 'You fall flat on your face!';
	
		protected function __construct()
		{
			$this->alias = new \Mechanics\Alias('bash', $this);
			parent::__construct();
		}
	
		public function perform(\Mechanics\Actor $actor, $chance = 0, $args = array())
		{
			$target = $actor->reconcileTarget($args);
			if(!$target)
				return;
			
			$roll = \Mechanics\Server::chance();
			$roll -= $actor->getRace()->getSize() * 1.25;
			$roll += $target->getRace()->getSize();
			$roll += $this->getNormalAttributeModifier($actor->getStr());
			
			$actor->incrementDelay(2);
			
			if($roll < $chance)
			{
				$a = new \Mechanics\Affect();
				$a->setAffect('stun');
				$a->setTimeout(1);
				$a->apply($target);
				return "You slam into " . $target->getAlias() . " and send " . $target->getSex() . " flying!";
			}
			
			return $this->fail_message;
		}
	}
?>
