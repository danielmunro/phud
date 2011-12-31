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
    use \Mechanics\Ability\Skill,
		\Mechanics\Actor,
    	\Mechanics\Server,
    	\Mechanics\Affect;

	class Bash extends Skill
	{
		protected $proficiency = 'melee';
		protected $required_proficiency = 20;
		protected $saving_attribute = 'str';

		protected function __construct()
		{
			self::addAlias('bash', $this);
		}

		public function getSubscriber()
		{
			return parent::getInputSubscriber('bash');
		}
	
		public function perform(Actor $actor, $percent, $args = [])
		{
			$target = $actor->reconcileTarget($args);
			if(!$target)
				return;
			
			$roll = Server::chance();
			$roll -= $actor->getRace()['lookup']->getSize() * 1.25;
			$roll += $target->getRace()['lookup']->getSize();
			$roll += $this->getNormalAttributeModifier($actor->getStr());
			
			$actor->incrementDelay(2);
			
			if($roll < $percent)
			{
				$a = new Affect();
				$a->setAffect('stun');
				$a->setTimeout(1);
				$a->apply($target);
				$sexes = [Actor::SEX_MALE=>'him',Actor::SEX_FEMALE=>'her',Actor::SEX_NEUTRAL=>'it'];
				return Server::out($actor, "You slam into ".$target->getAlias()." and send ".$target->getDisplaySex($sexes)." flying!");
			}
			
			return Server::out($actor, "You fall flat on your face!");
		}
	}
?>
