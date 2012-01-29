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
		\Mechanics\Tag,
		\Mechanics\Quest\Questmaster,
		\Mechanics\Quest\Instance as QuestInstance,
		\Mechanics\Quest\Quest as mQuest,
		\Mechanics\Command\User,
		\Living\User as lUser;

	class Quest extends User
	{
	
		protected $dispositions = array(Actor::DISPOSITION_STANDING, Actor::DISPOSITION_SITTING);
	
		protected function __construct()
		{
			self::addAlias('quest', $this);
		}
	
		public function perform(lUser $user, $args = array())
		{
			if(!$this->hasArgCount($user, $args, 2))
				return;
			
			$value = implode(' ', array_slice($args, 3));
			
			$dm_command = $this->getDMQuestCommand($args[1]);

			// User => Quest
			$users = $user->getRoom()->getActors();
			$target = null;
			foreach($users as $a)
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
				return $this->$command($target, $user, $value, $args);
			
			// DM => Quest
			if($target && $user->isDM() && $dm_command)
				return $this->$dm_command($target, $user, $value, $args);

			// User => Questmaster
			$target = $user->getRoom()->getUserByInput($args[2]);
			$command = $this->getQuestmasterCommand($args[1]);
			if($command && $target)
				return $this->$command($target, $user, $value, $args);

			$dm_command = $this->getDMQuestmasterCommand($args[1]);

			// DM => Questmaster
			if($target && $user->isDM() && $dm_command)
				return $this->$dm_command($target, $user, $value, $args);

			return Server::out($user, "There is no quest action like that. Try help quest.");
		}
		
		private function doList(Questmaster $questmaster, User $user, $value, $args)
		{
			$quests = $questmaster->getQuests();
			Server::out($user, $questmaster->getListMessage());
			array_walk(
				$quests,
				function($instance) use ($user)
				{
					Server::out($user, $instance->getQuest()->getShort().($instance->getQuest()->isQualified($user) ? '' : ' (unavailable)'));
				}
			);
		}
		
		private function doAccept(QuestInstance $instance, User $user, $value, $args)
		{
			$say = Alias::lookup('say');
			$questmaster = $instance->getUser();
			if(!$instance->getQuest()->isQualifiedToAccept($user, $quest))
				return $say->perform($questmaster, array($user, $questmaster->getNotQualifiedMessage()));
			
			if($instance)
			{
				$user->getQuestLog()->add(new QuestInstance($user, $instance->getQuest()));
				return $say->perform($questmaster, array($user, $questmaster->getAcceptMessage($instance)));
			}
		}
		
		private function doShort(QuestInstance $instance, User $user, $value, $args)
		{
			$old_short = $instance->getQuest()->getShort();
			$instance->getQuest()->setShort($value);
			return Server::out($user, ucfirst($old_short)." has been renamed to ".$value.".");
		}

		private function doNouns(QuestInstance $instance, User $user, $nouns, $args)
		{
			$instance->getQuest()->setNouns($nouns);
			Server::out($user, ucfirst($quest->getShort())."'s nouns set to: ".$quest->getNouns());
		}

		private function doGive(QuestInstance $instance, User $user, $null, $args)
		{
			$target = $user->getRoom()->getUserByInput($args[3]);
			if(!$target)
				return Server::out($user, "They aren't here.");
			$quest = $instance->getQuest();
			$user->getQuestLog()->remove($quest);
			$target->getQuestLog()->add($quest);
			Server::out($user, "You give the quest called ".$quest->getShort()." to ".$target->getAlias().".");
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
