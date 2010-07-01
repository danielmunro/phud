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
	class Cast extends \Mechanics\Command
	{
		
		protected function __construct()
		{
			\Mechanics\Command::addAlias(__CLASS__, array('c', 'cast'));
		}
		
		public static function perform(&$actor, $args = null)
		{
			if($args[1] == 'cure')
			{
				if(isset($args[2]) && strpos($args[2], 'l') === 0)
					$spell_name = 'Cure_Light';
				else
					$spell_name = 'Cure_Light';
			}
			
			/**
			 * Find all applicable spells and perform classes
			 */
			$spell = Skill::findByAliasAndName($actor->getAlias(), $spell_name);
			$perform = Perform::find('Spell_' . $spell_name);
			if(empty($spell))
				return Server::out($actor, "You can't cast that.");
			
			/**
			 * Figure out mana cost
			 */
			$mana_cost = 50;
			
			if($actor->getLevel() > $perform->getLevel())
				$mana_cost = $perform->getModifiedManaCost($actor);
			
			if($actor->getMana() < $mana_cost)
				return Server::out($actor, "You don't have enough mana for that.");
			
			/**
			 * Concentration test
			 */
			if(rand(0, 100) > $spell->getProficiency())
			{
				$actor->setMana($actor->getMana() - ($mana_cost / 2));
				return Server::out($actor, "You lost your concentration.");	
			}
			
			/**
			 * Announce to everyone
			 */
			$actors = ActorObserver::instance()->getActorsInRoom($actor->getRoom()->getId());
			foreach($actors as $a)
				if($a->getAlias() != $actor->getAlias())
					Server::out($a, $actor->getAlias(true) . " utters the words, '" . $perform->getName($actor) . "'");
			
			/**
			 * Perform
			 */
			$perform->perform($actor, $spell, $args);
			$perform->checkGain($actor, $spell);
			$actor->setMana($actor->getMana() - $mana_cost);
		}
	}
?>
