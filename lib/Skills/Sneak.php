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

	class Sneak extends Skill
	{
		protected $proficiency = 'stealth';
		protected $proficiency_required = 30;
		protected $saving_attribute = 'dex';

		protected function __construct()
		{
			self::addAlias('sneak', $this);
		}

		public function getSubscriber()
		{
			return parent::getInputSubscriber('sneak');
		}
		
		public function perform(Actor $actor, $chance = 0, $args = null)
		{
			$this->incrementDelay(1);
			$roll = Server::chance();
			
			$roll += $this->getNormalAttributeModifier($actor->getAttribute('dex'));
			
			$m = $actor->getAttribute('movement');
			$cost = -(round((0.05/min(1, $actor->getLevel()/10))*$m));
			$actor->modifyAttribute('movement', $cost);
			
			if($roll > $chance) {
				Server::out($actor, "Your attempt to move undetected fails.");
				return;
			}

			$a = new Affect();
			$a->setAffect('sneak');
			$a->setMessageAffect('Affect: sneak');
			$a->setMessageEnd('You no longer move silently.');
			$a->setTimeout(min(10, $actor->getAttrbute('dex') * 2));
			$att = $a->getAttributes();
			$att->setAttribute('str', $str);
			$att->setAttribute('dex', $dex);
			$a->apply($actor);
			$actor->getRoom()->announce2([
				['actor' => $actor, 'message' => 'You begin to move silently.'],
				['actor' => '*', 'message' => $actor.' fades into the shadows.']
			]);
		}
	}

?>
