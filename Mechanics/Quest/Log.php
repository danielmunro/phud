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
	class Log
	{
		protected $user = null;
		protected $quests = array();
	
		public function __construct(User $user)
		{
			$this->user = $user;
		}
		
		public function add(Quest $quest)
		{
			$this->quests[] = new Instance($this->user, $quest);
		}
		
		public function remove(Quest $quest)
		{
			$key = array_search($quest, $this->quests);
			if($key !== false)
				array_splice($this->quests, $key, 1);
		}

		public function getQuestByInput($input)
		{
			$quests = array_filter(
				$this->quests,
				function($qi) use ($input)
				{
					$nouns = explode(" ", $qi->getQuest()->getNouns());
					foreach($nouns as $noun)
						if(strpos($noun, $input) === 0)
							return true;
					return false;
				}
			);
			if($quests)
				return array_shift($quests);
		}
	}
?>
