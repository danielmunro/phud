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
			
			$value = implode(' ', array_slice($args, 3));
			
			$dm_command = $this->getDMQuestCommand($args[1]);

			// User => Quest
			$actors = $actor->getRoom()->getActors();
			$target = null;
			foreach($actors as $a)
			{
				if($a instanceof Questmaster)
				{
					$instance = $a->getQuestLog()->getQuestByInput($args[2]);
					if($instance)
					{
						$target = $instance;
						break;
					}
				}
			}
			$command = $this->getQuestCommand($args[1]);
			if($command && $target)
				return $this->$command($target, $actor, $value, $args);
			
			// DM => Quest
			if($target && $actor->isDM() && $dm_command)
				return $this->$dm_command($target, $actor, $value, $args);

			// User => Questmaster
			$target = $actor->getRoom()->getActorByInput($args[2]);
			$command = $this->getQuestmasterCommand($args[1]);
			if($command && $target)
				return $this->$command($target, $actor, $value, $args);

			$dm_command = $this->getDMQuestmasterCommand($args[1]);

			// DM => Questmaster
			if($target && $actor->isDM() && $dm_command)
				return $this->$dm_command($target, $actor, $value, $args);

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
		
		private function doAccept(QuestInstance $instance, Actor $actor, $value, $args)
		{
			$say = Alias::lookup('say');
			$questmaster = $instance->getActor();
			if(!$instance->getQuest()->isQualifiedToAccept($actor, $quest))
				return $say->perform($questmaster, array($actor, $questmaster->getNotQualifiedMessage()));
			
			if($instance)
			{
				$actor->getQuestLog()->add(new QuestInstance($actor, $instance->getQuest()));
				return $say->perform($questmaster, array($actor, $questmaster->getAcceptMessage($instance)));
			}
		}
		
		private function doShort(QuestInstance $instance, Actor $actor, $value, $args)
		{
			$old_short = $instance->getQuest()->getShort();
			$instance->getQuest()->setShort($value);
			return Server::out($actor, ucfirst($old_short)." has been renamed to ".$value.".");
		}

		private function doNouns(QuestInstance $instance, Actor $actor, $nouns, $args)
		{
			$instance->getQuest()->setNouns($nouns);
			Server::out($actor, ucfirst($quest->getShort())."'s nouns set to: ".$quest->getNouns());
		}

		private function doGive(QuestInstance $instance, Actor $actor, $null, $args)
		{
			$target = $actor->getRoom()->getActorByInput($args[3]);
			if(!$target)
				return Server::out($actor, "They aren't here.");
			$quest = $instance->getQuest();
			$actor->getQuestLog()->remove($quest);
			$target->getQuestLog()->add($quest);
			Server::out($actor, "You give the quest called ".$quest->getShort()." to ".$target->getAlias().".");
		}

		private function getQuestCommand($input)
		{
			return $this->command(array('accept'), $input);
		}

		private function getQuestmasterCommand($input)
		{
			return $this->command(array('list'), $input);
		}
		
		private function getDMQuestCommand($input)
		{
			return $this->command(array('create', 'short', 'nouns', 'give'), $input);
		}

		private function getDMQuestmasterCommand($input)
		{
			return $this->command(array(), $input);
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
