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
	class Armor extends \Mechanics\Spell
	{
	
		protected static $name_familiar = 'armor';
		protected static $name_unfamiliar = 'plysoxix';
	
		public function __construct($percent, $actor_id = null, $actor_type = '')
		{
			parent::__construct($percent, self::TYPE_SPELL, $actor_id, $actor_type, array('ar', 'armor'));
		}
	
		public static function perform(\Mechanics\Actor &$actor, &$target, $args = null)
		{
		
			if(!($target instanceof \Mechanics\Actor))
				return \Mechanics\Server::out($actor, "You cannot cast that on that.");
			
			$timeout = 1 + ceil($actor->getLevel() * 0.9);
			
			$modifier = max(floor($actor->getLevel() / 10), 1);
			$mod_ac = -15 * $modifier;
			
			// new \Mechanics\Affect(self::$name_familiar, 'Spell: armor: ' . $mod_ac . ' to armor class', $timeout, array('mod_ac' => $mod_ac))
			$a = new \Mechanics\Affect();
			$a->setAffect(self::$name_familiar);
			$a->setMessageAffect('Spell: armor: '.$mod_ac.' to armor class');
			$a->setMessageEnd('You feel less protected.');
			$a->setTimeout($timeout);
			$atts = $a->getAttributes();
			$atts->setAcBash($mod_ac);
			$atts->setAcSlash($mod_ac);
			$atts->setAcPierce($mod_ac);
			$atts->setAcMagic($mod_ac);
			$target->addAffect($a);
			\Mechanics\Server::out($target, "You feel more protected!");
			return false;
		}
	}
?>
