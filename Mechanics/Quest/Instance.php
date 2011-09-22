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
	namespace Mechanics\Quest;
	use \Living\User;
	class Instance
	{
		protected $quest = null;
		protected $actor = null;
		protected $requirements_progress = null;
		
		public function __construct(Actor $actor, Quest $quest)
		{
			$this->actor = $actor;
			$this->quest = $quest;
			$this->requirements_progress = new Requirements($this->quest->getRequirementsToComplete());
		}
		
		public function getActor()
		{
			return $this->actor;
		}
		
		public function getQuest()
		{
			return $this->quest;
		}
		
		public function getRequirementsProgress()
		{
			return $this->requirements_progress;
		}
		
		public function isQualifiedToComplete()
		{
			return $this->quest->isQualifiedToComplete($this->actor);
		}

		public function getExperience()
		{
			$quest_level = $this->quest->getRequirementsToAccept()->getLevel();
			$actor_level = $this->actor->getLevel();
			$mod = 0.1 * ($actor_level - $quest_level);
			return $this->quest->getExperience() * $mod;
		}
	}
?>
