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
    use \Mechanics\Ability\Ability,
		\Mechanics\Ability\Skill,
    	\Mechanics\Actor,
    	\Mechanics\Server,
		\Mechanics\Affect,
    	\Mechanics\Race;

	class Fear extends Skill
	{
		protected $proficiency = 'maladictions';
		protected $proficiency_required = 25;
		protected $saving_attribute = 'cha';

		protected function __construct()
		{
			self::addAlias('fear', $this);
		}

		public function getSubscriber()
		{
			return $this->getInputSubscriber('fear');
		}
	
		public function perform(Actor $actor, $proficiency, $args = [])
		{
			$target = $actor->reconcileTarget($args);
			if(!$target) {
				return Server::out($actor, "Who are you trying to scare?");
			}
			$saves = $this->calculateSaves($actor, $target);
			echo "Raw saves: ".$saves.", ";
			$saves = Server::_range(5, 95, $saves);
			$actor->incrementDelay(2);
			$actor->setMovement($actor->getMovement() - 2);
			echo "Saves: ".$saves."\n";
			if($saves > Server::chance()) {
				$a = new Affect();
				$a->setAffect('fear');
				$a->setTimeout(max(2, round($proficiency / 10)));
				$a->setMessageAffect('Affect: fear. Decrease strength and constitution');
				$a->setMessageEnd('You are no longer afraid.');
				$mod = -(round($proficiency / 20));
				$atts = $a->getAttributes();
				$atts->setStr($mod);
				$atts->setCon($mod);
				$a->apply($target);
				foreach($target->getRoom()->getActors() as $room_actor) {
					if($room_actor === $actor) {
						Server::out($actor, "You scare ".$target."!");
					} else if($room_actor === $target) {
						Server::out($target, "You become frightened!");
					} else {
						Server::out($room_actor, ucfirst($actor)." scares ".$target."!");
					}

				}
				return;
			}
			Server::out($actor, "You fail to scare ".$target.".");
	 	}
	
	}

?>
