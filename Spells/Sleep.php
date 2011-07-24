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
	namespace Spells;
	class Sleep extends \Mechanics\Spell
	{
	
		protected $name_familiar = 'sleep';
		protected $name_unfamiliar = 'teruo';
		protected $spell_type = self::TYPE_OFFENSIVE;
		protected $aliases = array('sleep', 'sle', 'slee');
		
		public static function perform(\Mechanics\Actor &$actor, \Mechanics\Actor &$target, $args = null)
		{
			
			$timeout = 1 + ceil($actor->getLevel() * 0.9);
			$target->setDisposition(\Mechanics\Actor::DISPOSITION_SLEEPING);
			$a = new \Mechanics\Affect();
			$a->setAffect(self::$name_familiar);
			$a->setMessageAffect('Spell: sleep');
			$a->setTimeout($timeout);
			$target->addAffect($a);
			$target->getRoom()->announce($target, $target->getAlias(true)." goes to sleep.");
			\Mechanics\Server::out($target, "You go to sleep.");
		}
	}

?>
