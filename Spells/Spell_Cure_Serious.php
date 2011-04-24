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

	class Spell_Cure_Serious extends Perform
	{
	
		protected $level = 10;
		protected $display_name = array('cure serious', 'frzzz flam');
		protected $improve_by_practice = 0;
		protected $min_mana_cost = 25;
	
		public function __construct($percent, $actor_id = null, $actor_type = '')
		{
			parent::__construct($percent, self::TYPE_SPELL, $actor_id, $actor_type, array('cure serious', 'cure s'));
		}
	
		public static function perform(Actor &$actor, Skill $spell, $args = null)
		{
		
			$amount = 5 + $actor->getLevel() / 2;
			$actor->setHp($actor->getHp() + (int) $amount);
			
			Server::out($actor, "You feel better!");
		}
		
		public function getName() { return 'Cure_Serious'; }
		
	}

?>
