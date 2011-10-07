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
    use \Mechanics\Ability\Spell;
    use \Mechanics\Affect;
    use \Mechanics\Server;

	class Armor extends Spell
	{
	
		protected $name_familiar = 'armor';
		protected $name_unfamiliar = 'plysoxix';
		
		protected function initSpellGroup()
		{
			$this->spell_group = \Spell_Groups\Protective::instance();
		}
		
		public function perform(Actor $actor, $args = array())
		{
			$timeout = 1 + ceil($actor->getLevel() * 0.9);
			
			$modifier = max(floor($actor->getLevel() / 10), 1);
			$mod_ac = -15 * $modifier;
			
			$target = $actor; //HACK
			
			$a = new Affect();
			$a->setAffect(self::$name_familiar);
			$a->setMessageAffect('Spell: armor: '.$mod_ac.' to armor class');
			$a->setMessageEnd('You feel less protected.');
			$a->setTimeout($timeout);
			$atts = $a->getAttributes();
			$atts->setAcBash($mod_ac);
			$atts->setAcSlash($mod_ac);
			$atts->setAcPierce($mod_ac);
			$atts->setAcMagic($mod_ac);
			$a->apply($target);
			Server::out($target, "You feel more protected!");
			return false;
		}
	}
?>
