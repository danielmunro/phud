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
	use \Mechanics\Alias;
	use \Mechanics\Actor;
	use \Mechanics\Server;
	class Score extends \Mechanics\Command
	{
	
		protected function __construct()
		{
			new Alias('score', $this);
		}
	
		public function perform(Actor $actor, $args = array())
		{
			Server::out($actor, 'You are ' . $actor->getAlias() . ', a ' . $actor->getRace().' '.$actor->getDisciplineFocus());
			Server::out($actor, 'Attributes: Str ' . $actor->getBaseStr() . '(' . $actor->getStr() . ') ' .
			'Int ' . $actor->getBaseInt() . '(' . $actor->getInt() . ') ' . 
			'Wis ' . $actor->getBaseWis() . '(' . $actor->getWis() . ') ' .
			'Dex ' . $actor->getBaseDex() . '(' . $actor->getDex() . ') ' .
			'Con ' . $actor->getBaseCon() . '(' . $actor->getCon() . ')');
			
			Server::out(
				$actor, 'Hp: ' . $actor->getHp() . '/' . $actor->getMaxHp() .
				' Mana: ' . $actor->getMana() . '/' . $actor->getMaxMana() .
				' Movement: ' . $actor->getMovement() . '/' . $actor->getMaxMovement());
			
			$experience = (int) ($actor->getExperiencePerLevel() - ($actor->getExperience() % $actor->getExperiencePerLevel()));
			Server::out($actor,
				'Level ' . $actor->getLevel() . ', ' . $experience . ' experience to next level');
			Server::out($actor,
				$actor->getGold() . ' gold, ' . $actor->getSilver() . ' silver, ' . $actor->getCopper() . ' copper.');
		
			Server::out($actor, 'You are' . self::getAcString($actor->getAcBash()) . 'against bashing.');
			Server::out($actor, 'You are' . self::getAcString($actor->getAcSlash()) . 'against slashing.');
			Server::out($actor, 'You are' . self::getAcString($actor->getAcPierce()) . 'against piercing.');
			Server::out($actor, 'You are' . self::getAcString($actor->getAcMagic()) . 'against magic.');
		
		}
		
		private static function getAcString($ac)
		{
			if($ac >= 100)
				return " hopelessly vulnerable to ";
			if($ac >= 80)
				return " defenseless against ";
			if($ac >= 60)
				return " barely protected from ";
			if($ac >= 40)
				return " slightly armored against ";
			if($ac >= 20)
				return " somewhat armored against ";
			if($ac >= 0)
				return " armored against ";
			if($ac >= -20)
				return " well-armored against ";
			if($ac >= -40)
				return " very well-armored against ";
			if($ac >= -60)
				return " heavily armored against ";
			if($ac >= -80)
				return " superbly armored against ";
			if($ac >= -100)
				return " almost invulnerable to ";
			return " divinely armored against ";
		}
	
	}

?>
