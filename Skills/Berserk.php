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
	class Berserk extends \Mechanics\Ability
	{
	
		protected static $aliases = array('bers', 'berserk');
	
		public function __construct($percent, $actor_id = null, $actor_type = '')
		{
			parent::__construct($percent, self::TYPE_SKILL, $actor_id, $actor_type);
		}
	
		public function perform(\Mechanics\Actor &$actor, $args = null)
		{
			
			//$actor->incrementDelay(2);
			
			$chance = rand(0, 100);
			if($chance > $this->getPercent())
				return \Mechanics\Server::out($actor, "Your face gets really red!");
			
			$p = $actor->getLevel() / \Mechanics\Actor::MAX_LEVEL;
			$timeout = ceil(10 * $p);
			$str = ceil(4 * $p);
			$dex = ceil(2 * $p);
			$a = new \Mechanics\Affect();
			$a->setAffect('berserk');
			$a->setMessageAffect('Affect: berserk');
			$a->setMessageEnd('You cool down.');
			$a->setTimeout($timeout);
			$att = $a->getAttributes();
			$att->setStr($str);
			$att->setDex($dex);
			$actor->addAffect($a);
			\Mechanics\Server::out($actor, "You fly into a rage!");
		}
	}

?>
