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
	use \Living\User as User;
	class Quest
	{
		protected $short = 'a generic quest';
		protected $nouns = 'generic quest';
		protected $experience = 0;
		protected $requirements_to_accept = null;
		protected $requirements_to_complete = null;
		protected $hooks = array();
		protected static $instances = array();
		
		const HOOK_CREATE = 'create';
	
		public function __construct()
		{
			$this->requirements_to_accept = new Requirements();
			$this->requirements_to_complete = new Requirements();
		}
	
		public function addHook($hook)
		{
			if($hook === self::HOOK_CREATE)
			{
				$this->hooks[] = $hook;
				return;
			}
			// err
		}
		
		public function isQualifiedToAccept(User $user, Questmaster $questmaster = null)
		{
			return $this->requirements_to_accept->isQualified($user, $questmaster);
		}
		
		public function isQualifiedToComplete(User $user, Questmaster $questmaster = null)
		{
			return $this->requirements_to_complete->isQualified($user, $questmaster);
		}
		
		public function getRequirementsToAccept()
		{
			return $this->requirements_to_accept;
		}

		public function getRequirementsToComplete()
		{
			return $this->requirements_to_complete;
		}
		
		public function getMinimumLevels()
		{
			return $this->minimum_level;
		}
		
		public function getShort()
		{
			return $this->short;
		}
		
		public function getNouns()
		{
			return $this->nouns;
		}
		
		public function getHooks()
		{
			return $this->hooks;
		}
		
		public function getExperience()
		{
			return $this->experience;
		}
		
		public function setExperience($experience)
		{
			$this->experience = $experience;
		}

		public function setShort($short)
		{
			$this->short = $short;
		}
		
		public function setNouns($nouns)
		{
			$this->nouns = $nouns;
		}
		
		public function __toString()
		{
			return $this->short;
		}
	}
?>
