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
	class Armor extends \Mechanics\Ability
	{
	
		protected static $display_name = array('armor', 'plysoxix');
		private static $base_chance = 99;
	
		public static function perform(\Mechanics\Actor &$actor, \Mechanics\Actor &$target, $args = null)
		{
		
			$this->apply($actor, $target);
			return "You feel more protected!";
		}
		
		public function apply($caster, $target, $timeout = null)
		{
			
			if(!$timeout)
				$timeout = $caster->getLevel();
			
			return new Affect(
								$target,
								__CLASS__,
								function($target)
								{
									$target->setAcSlash($target->getAcSlash() - 15);
									$target->setAcBash($target->getAcBash() - 15);
									$target->setAcPierce($target->getAcPierce() - 15);
									$target->setAcMagic($target->getAcMagic() - 15);
								},
								function($target)
								{
									$target->setAcSlash($target->getAcSlash() + 15);
									$target->setAcBash($target->getAcBash() + 15);
									$target->setAcPierce($target->getAcPierce() + 15);
									$target->setAcMagic($target->getAcMagic() + 15);
								},
								$timeout,
								'You feel less protected.'
							);
		}
		
		public function getDisplayName($index = 1)
		{
			return self::$display_name[$index];
		}
	}

?>
