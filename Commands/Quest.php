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
	use \Mechanics\Actor;
	use \Mechanics\Alias;
	use \Mechanics\Server;
	use \Mechanics\Tag;
	use \Mechanics\Quest\Questmaster;
	use \Mechanics\Quest\Instance as QuestInstance;
	use \Mechanics\Quest\Quest as mQuest;
	class Quest extends \Mechanics\Command
	{
	
		protected $dispositions = array(Actor::DISPOSITION_STANDING, Actor::DISPOSITION_SITTING);
	
		protected function __construct()
		{
			new Alias('quest', $this);
		}
	
		public function perform(Actor $actor, $args = array())
		{
			if(!$this->hasArgCount($actor, $args, 2))
				return;
			
			$command = $this->getCommand($args[1]);
			if(!$command && $actor->isDM())
				$command = $this->getDMCommand($args[1]);
			
			$target = $actor->getRoom()->getActorByInput($args[2]);
			if(!($target instanceof Questmaster))
				return Server::out($actor, "You don't see them anywhere.");
			
			$value = implode(' ', array_slice($args, 3));
			
			if($command)
				$this->$command($target, $actor, $value, $args);

			return Server::out($actor, "There is no quest action like that. Try help quest.");
		}
		
		private function doList(Questmaster $questmaster, Actor $actor, $value, $args)
		{
			$quests = $questmaster->getQuests();
			Server::out($actor, $questmaster->getListMessage());
			array_walk(
				$quests,
				function($instance) use ($actor)
				{
					Server::out($actor, $instance->getQuest()->getShort().($instance->getQuest()->isQualified($actor) ? '' : ' (unavailable)'));
				}
			);
		}
		
		private function doAccept(Questmaster $questmaster, Actor $actor, $value, $args)
		{
			$quest_instance = $questmaster->getQuestLog()->getQuestByInput($args[1]);
			if(!$quest_instance)	
				$quest_instance = $questmaster->getQuestLog()->getQuestByInput($args[2]);
			
			if(!$quest_instance->getQuest()->isQualifiedToAccept($actor, $questmaster))
				return;
			
			if($quest_instance)
			{
				$actor->getQuestLog()->add(new QuestInstance($actor, $quest_instance->getQuest()));
				return Server::out($actor, "You accept ".$questmaster->getAlias()."'s quest: ".$quest_instance->getQuest().".");
			}
		}
		
		private function doCreate(Questmaster $questmaster, Actor $actor, $value, $args)
		{
			$questmaster->getQuestLog()->add(new mQuest());
			Server::out($actor, $questmaster->getAlias(true)." has obtained a new quest!");
		}
		
		private function doShort(Questmaster $questmaster, Actor $actor, $value, $args)
		{
			$quest_instance = $questmaster->getQuestLog()->getQuestByInput($args[3]);
			if($quest_instance)
			{
				$value = implode(" ", array_slice($args, 4));
				$old_short = $quest_instance->getQuest()->getShort();
				$quest_instance->getQuest()->setShort($value);
				return Server::out($actor, ucfirst($old_short)." has been renamed to ".$value.".");
			}
			Server::out($actor, "What quest?");
		}
		
		private function getCommand($input)
		{
			return $this->command(array('list', 'accept'), $input);
		}
		
		private function getDMCommand($input)
		{
			return $this->command(array('create', 'short'), $input);
		}
		
		private function command($commands, $input)
		{
			foreach($commands as $command)
			{
				if(strpos($command, $input) === 0)
					return 'do'.ucfirst($command);
			}
		}
	}
?>
