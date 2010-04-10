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

	class QuestmasterSid extends Questmaster
	{
		
		public function __construct()
		{
			$this->alias = 'Sid';
			$this->noun = 'sid gambler';
			$this->description = 'A notorious gambler stands before you, fidgeting with poker chips.';
			$this->level = 1;
			$this->setRace('human');
			$this->fightable = false;
			
			$this->inventory = Inventory::find('quest', 1);
		
			parent::__construct(6);
		}

		public function questInfo(&$actor)
		{
			
			$quest = Quest::find($actor->getId(), 1);
			
			if(!$quest->getAccepted())
				return Command_Say::perform($this, "Thank goodness you've arrived. A giant rat from the wine cellar has stolen some of my poker chips! Chase him down and slay him and I will give you a reward.");
			if($quest->getAccepted() && !$quest->getComplete())
				return Command_Say::perform($this, "Ah no luck yet? Well keep at it!");
			if($quest->getComplete() && !$quest->getAwardObtained())
				return $this->questAward($actor);
			if($quest->getAwardObtained())
				return Command_Say::perform($this, "Now I can get back to business.");
		}
		
		public function questAward(&$actor)
		{
			
			$quest = Quest::find($actor->getId(), 1);
			if($quest->getComplete() && !$quest->getAwardObtained())
			{
				$quest->setAwardObtained(true);
				$actor->awardExperience(1000);
				$this->silver += 2;
				Command_Say::perform($this, "Thank you so much " . $actor->getAlias(true) . "! Here's a fair cut of the loot.");
				Command_Give::perform($this, array('give', '2', 'silver', $actor->getAlias()));
				Server::out($actor, "You got a " . Tag::apply('Quest Award') . "2 silver.");
				Server::out($actor, "You got a " . Tag::apply('Quest Award') . "1000 experience.");
			}
		}
		
		public function questAccept(&$actor)
		{
		
			$quest = Quest::find($actor->getId(), 1);
			if($quest->getAccepted())
				return Command_Say::perform($this, "You've already accepted this quest.");
			
			$quest->setAccepted(true);
			Command_Say::perform($this, "Ah I knew a brave soul such as yourself would come along eventually. Let me give you this key so you can get in the wine cellar to the north.");
			$this->inventory->add(new Item(0, "An old key to the Temple wine cellar is here.", "an old brass key", "brass key", 0, 0.1, 100, 'key', true, '', null, 2));
			Command_Give::perform($this, array('give', 'key', $actor->getAlias()));
		}
		
		public function questDone(&$actor)
		{
		
			$item = $actor->getInventory()->getItemByInput(array('', 'chips'));
			$quest = Quest::find($actor->getId(), 1);
			
			if($quest->getAwardObtained())
				return Command_Say::perform($this, "Now I can get back to business.");
			
			if($item instanceof Item && $item->getType() == 'quest')
			{
				$quest->setComplete(true);
				$actor->getInventory()->remove($item);
				$this->questAward($actor);
			}
			else
				Command_Say::perform($this, "I don't see no stinkin' poker chips.");
		}
	}

?>
