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
	use \Mechanics\Ability\Spell,
		\Mechanics\Actor,
		\Mechanics\Affect,
		\Mechanics\Server;

	class Sleep extends Spell
	{
		protected $proficiency = 'beguiling';
		protected $required_proficiency = 40;
		protected $affect = null;

		protected function __construct()
		{
			self::addAlias('sleep', $this);
			$this->affect = new Affect();
			$this->affect->setAffect('sleep');
			$this->affect->setMessageAffect('Spell: sleep');
		}
		
		public function perform(Actor $caster, Actor $target, $proficiency, $args = [])
		{
			$timeout = round(1 + ($proficiency / 10));
			$target->setDisposition(Actor::DISPOSITION_SLEEPING);
			$this->affect->setTimeout($timeout);
			$this->affect->apply($target);
			$target->getRoom()->announce($target, ucfirst($target)." goes to sleep.");
			Server::out($target, "You go to sleep.");
		}
	}

?>
