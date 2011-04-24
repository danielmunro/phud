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
	
		protected $name_familiar = 'armor';
		protected $name_unfamiliar = 'plysoxix';
	
		public function __construct($percent, $actor_id = null, $actor_type = '')
		{
			parent::__construct($percent, self::TYPE_SPELL, $actor_id, $actor_type, array('ar', 'armor'));
		}
	
		public static function perform(\Mechanics\Actor &$actor, &$target, $args = null)
		{
		
			if(!($target instanceof \Mechanics\Actor))
				return \Mechanics\Server::out($actor, "You cannot cast that on that.");
			
			$timeout = floor((10 * \Mechanics\Server::PULSES_PER_TICK) + (20 * \Mechanics\Server::PULSES_PER_TICK * ($actor->getLevel() / \Mechanics\Actor::MAX_LEVEL)));
			
			$modifier = max(floor($actor->getLevel() / 10), 1);
			$mod_ac = -15 * $modifier;
			
			new \Mechanics\Affect(__CLASS__, $target, 'Spell: armor: ' . $mod_ac . ' to armor class',  array('mod_ac' => $mod_ac, 'timeout' => $timeout));
			\Mechanics\Server::out($target, "You feel more protected!");
			return false;
		}
		
		public static function apply(&$target, $args, $affect)
		{
			
			$target->setAcSlash($target->getAcSlash() + $args['mod_ac']);
			$target->setAcBash($target->getAcBash() + $args['mod_ac']);
			$target->setAcPierce($target->getAcPierce() + $args['mod_ac']);
			$target->setAcMagic($target->getAcMagic() + $args['mod_ac']);
			
			\Mechanics\Pulse::instance()->registerEvent
			(
				$args['timeout'],
				function($args)
				{
					\Mechanics\Server::out($args[0], "You feel less protected.");
					$args[2]->removeAffectFrom($args[0]);
					$args[0]->setAcSlash($args[0]->getAcSlash() - $args[1]);
					$args[0]->setAcBash($args[0]->getAcBash() - $args[1]);
					$args[0]->setAcPierce($args[0]->getAcPierce() - $args[1]);
					$args[0]->setAcMagic($args[0]->getAcMagic() - $args[1]);
				},
				array($target, $args['mod_ac'], $affect)
			);
		}
	}
?>
