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
	use \Living\User,
		\Mechanics\Alias,
		\Mechanics\Server,
		\Mechanics\Item,
		\Mechanics\Inventory;

	class Requirements
	{
		protected $races = [];
		protected $level = 0;
		protected $inventory = null;
		protected $previous_quests = [];
		
		public function __construct(Requirements $requirements = null)
		{
			if($requirements)
			{
				$this->races = $requirements->getRaces();
				$this->level = $requirements->getLevel();
				$this->inventory = $requirements->getInventory();
				$this->previous_quests = $requirements->getPreviousQuests();
				return;
			}
			$this->inventory = new Inventory();
		}
		
		public function getRaces()
		{
			return $this->races;
		}
		
		public function getLevel()
		{
			return $this->level;
		}
		
		public function getInventory()
		{
			return $this->inventory;
		}
		
		public function getPreviousQuests()
		{
			return $this->previous_quests;
		}
		
		public function isQualified(User $user, Questmaster $questmaster = null)
		{
			$say = Alias::lookup('say');
			
			if($user->getLevel() < $this->level)
			{
				if($questmaster)
					$say->perform($questmaster, "You're too inexperienced for this quest");
				return false;
			}
			if((empty($this->races) || in_array($user->getRace(), $this->races)))
			{
				if(sizeof($this->getInventory()->getItems()))
				{
					$missing = array_diff($this->getInventory()->getItems(), $user->getInventory()->getItems());
					if($missing)
					{
						if($questmaster)
							$say->perform($questmaster, "You are missing these items: ".implode(", ", $missing));
						return false;
					}
				}
				if(sizeof($this->previous_quests))
				{
					$quests = $this->previous_quests;
					foreach($user->getQuestLog()->getQuests() as $quest_instance)
					{
						$key = array_search($quest_instance->getQuest(), $this->previous_quests);
						if($key !== false)
							array_splice($quests, $key, 1);
					}
					if($quests)
					{
						if($questmaster)
							$say->perform($questmaster, "You are missing these quests: ".implode(", ", $quests));
						return false;
					}
				}
				return true;
			}
			return false;
		}
	}
?>
