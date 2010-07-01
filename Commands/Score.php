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
	namespace Commands;
	class Score extends \Mechanics\Command
	{
	
		protected function __construct()
		{
		
			\Mechanics\Command::addAlias(__CLASS__, array('sc', 'score'));
		}
	
		public static function perform(&$actor, $args = null)
		{
		
			\Mechanics\Server::out($actor, 'You are ' . $actor->getAlias() . ', a(n) ' . $actor->getRaceStr());
			\Mechanics\Server::out($actor, 'Attributes: Str (' . $actor->getStr() . ') ' .
			'Int (' . $actor->getInt() . ') ' . 
			'Wis (' . $actor->getWis() . ') ' .
			'Dex (' . $actor->getDex() . ') ' .
			'Con (' . $actor->getCon() . ')');
			
			\Mechanics\Server::out(
				$actor, 'Hp: ' . $actor->getHp() . '/' . $actor->getMaxHp() .
				' Mana: ' . $actor->getMana() . '/' . $actor->getMaxMana() .
				' Movement: ' . $actor->getMovement() . '/' . $actor->getMaxMovement());
			
			$experience = (int) ($actor->getExpPerLevel() - ($actor->getExperience() % $actor->getExpPerLevel()));
			\Mechanics\Server::out($actor,
				'Level ' . $actor->getLevel() . ', ' . $experience . ' experience to next level');
			\Mechanics\Server::out($actor,
				$actor->getGold() . ' gold, ' . $actor->getSilver() . ' silver, ' . $actor->getCopper() . ' copper.');
		
		}
	
	}

?>
