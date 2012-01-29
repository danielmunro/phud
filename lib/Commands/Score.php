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
	use \Mechanics\Alias,
		\Mechanics\Actor,
		\Mechanics\Server,
		\Mechanics\Command\User,
		\Living\User as lUser;

	class Score extends User
	{
		protected function __construct()
		{
			self::addAlias('score', $this);
		}
	
		public function perform(lUser $user, $args = array())
		{
			Server::out($user, 'You are ' . $user . ', a ' . $user->getRace()['alias']);
			Server::out($user, 'Attributes: '.
				'Str '.$user->getAttribute('str').'('.$user->getUnmodifiedAttribute('str').') ' .
				'Int '.$user->getAttribute('int').'('.$user->getUnmodifiedAttribute('int').') ' . 
				'Wis '.$user->getAttribute('wis').'('.$user->getUnmodifiedAttribute('wis').') ' .
				'Dex '.$user->getAttribute('dex').'('.$user->getUnmodifiedAttribute('dex').') ' .
				'Con '.$user->getAttribute('con').'('.$user->getUnmodifiedAttribute('con').') ' .
				'Cha '.$user->getAttribute('cha').'('.$user->getUnmodifiedAttribute('cha').')');
			
			Server::out(
				$user, 'Hp: ' . $user->getAttribute('hp') . '/' . $user->getMaxAttribute('hp') .
				' Mana: ' . $user->getAttribute('mana') . '/' . $user->getMaxAttribute('mana') .
				' Movement: ' . $user->getAttribute('movement') . '/' . $user->getMaxAttribute('movement'));
			
			//$experience = round($user->getExperiencePerLevel() - ($user->getExperience() % $user->getExperiencePerLevel()));
			$experience = 1;
			Server::out($user,
				'Level ' . $user->getLevel() . ', ' . $experience . ' experience to next level');
			Server::out($user,
				$user->getGold() . ' gold, ' . $user->getSilver() . ' silver, ' . $user->getCopper() . ' copper.');
		
			Server::out($user, 'You are' . self::getAcString($user->getAttribute('ac_bash')) . 'against bashing.');
			Server::out($user, 'You are' . self::getAcString($user->getAttribute('ac_slash')) . 'against slashing.');
			Server::out($user, 'You are' . self::getAcString($user->getAttribute('ac_pierce')) . 'against piercing.');
			Server::out($user, 'You are' . self::getAcString($user->getAttribute('ac_magic')) . 'against magic.');
		
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
