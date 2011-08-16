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
	class Berserk extends \Mechanics\Skill
	{
	
		protected $creation_cost = 5;
		protected $fail_message = 'Your face gets really red!';
		protected $delay = 2;
	
		protected function __construct()
		{
			$this->alias = new \Mechanics\Alias('berserk', $this);
			$this->base_class = \Disciplines\Berzerker::instance();
			parent::__construct();
		}
	
		public function perform(\Mechanics\Actor $actor, $chance = 0, $args = null)
		{
			if(rand(0, 100) > $chance)
				return $this->fail_message;
			
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
			return "You fly into a rage!";
		}
	}

?>
