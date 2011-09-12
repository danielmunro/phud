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
	namespace Mechanics;
	class Quest
	{
		protected $short = 'a generic quest';
		protected $minimum_level = 1;
		protected $objectives = array();
		
		public function isQualified(Actor $actor)
		{
			if($actor->getLevel() < $this->minimum_level)
				return false;
			
			$allowed = true;
			array_walk(
				$this->objectives,
				function($o) use (&$allowed, $actor)
				{
					if(!$o->isQualified($actor))
						$allowed = false;
				}
			);
			return $allowed;
		}
		
		public function getObjectives()
		{
			return $this->objectives;
		}
		
		public function addObjective(Objective $objective)
		{
			$this->objectives[] = $objective;
		}
		
		public function removeObjective(Objective $objective)
		{
			$key = array_search($objective, $this->objectives);
			if($key !== false)
			{
				unset($this->objectives[$key]);
				$this->objectives = array_values($this->objectives);
			}
		}
		
		public function getMinimumLevels()
		{
			return $this->minimum_level;
		}
		
		public function setShort($short)
		{
			$this->short = $short;
		}
		
		public function getShort()
		{
			return $this->short;
		}
		
		public function getExperience(Actor $actor)
		{
			$experience = 0;
			array_walk(
				$this->objectives,
				function($o) use (&$experience, $actor)
				{
					$experience += $o->getExperience($actor);
				}
			);
			return $experience;
		}
	}
?>
