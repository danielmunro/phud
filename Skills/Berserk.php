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
    use \Mechanics\Ability\Skill;
    use \Mechanics\Actor;
    use \Mechanics\Server;
    use \Mechanics\Affect;
    use \Disciplines\Warrior;

	class Berserk extends Skill
	{
	
        protected static $alias = 'berserk';
        protected static $level = 15;
		protected static $creation_points = 5;
		
		public function perform(Actor $actor, $chance = 0, $args = null)
		{
			$this->incrementDelay(2);
			$roll = Server::chance();
			
			$roll += $this->getHardAttributeModifier($actor->getDex());
			$roll += $this->getNormalAttributeModifier($actor->getStr());
			
			if($actor->getDisciplinePrimary() === Warrior::instance())
				$roll -= 10;
			
			$actor->setMovement($actor->getMovement() / 2);
			$actor->setMana($actor->getMana() / 2);
			
			if($roll > $chance)
				return $this->fail_message;
			
			$p = $actor->getLevel() / Actor::MAX_LEVEL;
			$timeout = ceil(10 * $p);
			$str = ceil(4 * $p);
			$dex = ceil(2 * $p);
			$a = new Affect();
			$a->setAffect('berserk');
			$a->setMessageAffect('Affect: berserk');
			$a->setMessageEnd('You cool down.');
			$a->setTimeout($timeout);
			$att = $a->getAttributes();
			$att->setStr($str);
			$att->setDex($dex);
			$a->apply($actor);
			return "You fly into a rage!";
		}
	}

?>
