<?php

	class Command_Room extends Command
	{
		
		public static function perform(&$actor, $args = null)
		{
		
			if($actor->getAlias() != 'Dan')
				return Server::out($actor, "You cannot do that.");
			
			if($args[1] == 'new')
			{
				$room = new Room();
				$room->save();
				$room->setInventory(Inventory::find('room', $room->getId()));
				$direction = $args[2];
				$actor->getRoom()->{'set' . ucfirst($direction)}($room->getId());
				
				if($direction == 'north')
					$new_direction = 'south';
				if($direction == 'south')
					$new_direction = 'north';
				if($direction == 'east')
					$new_direction = 'west';
				if($direction == 'west')
					$new_direction = 'east';
				if($direction =='up')
					$new_direction = 'down';
				if($direction == 'down')
					$new_direction = 'up';
				
				$room->{'set' . ucfirst($new_direction)}($actor->getRoom()->getId());
				$room->save();
				$actor->getRoom()->save();
				
				return Server::out($actor, "You've created a new room to the " . $direction . ".");
			}
			
			if($args[1] == 'id')
				return Server::out($actor, "ID: " . $actor->getRoom()->getId());
			
			if($args[1] == 'title')
			{
				array_shift($args);
				array_shift($args);
				$title = implode(' ', $args);
				$actor->getRoom()->setTitle($title);
				$actor->getRoom()->save();
				return Server::out($actor, "Title set to: " . $title);
			}
			
			if($args[1] == 'description')
			{
				array_shift($args);
				array_shift($args);
				$description = implode(' ', $args);
				$actor->getRoom()->setDescription($description);
				$actor->getRoom()->save();
				return Server::out($actor, "Description set to: " . $description);
			}
			
			if($args[1] == 'north')
			{
				$actor->getRoom()->setNorth($args[2]);
				$actor->getRoom()->save();
				return Server::out($actor, "North changed");
			}
			
			if($args[1] == 'south')
			{
				$actor->getRoom()->setSouth($args[2]);
				$actor->getRoom()->save();
				return Server::out($actor, "South changed");
			}
			
			if($args[1] == 'east')
			{
				$actor->getRoom()->setEast($args[2]);
				$actor->getRoom()->save();
				return Server::out($actor, "East changed");
			}
			
			if($args[1] == 'west')
			{
				$actor->getRoom()->setWest($args[2]);
				$actor->getRoom()->save();
				return Server::out($actor, "West changed");
			}
			
			if($args[1] == 'up')
			{
				$actor->getRoom()->setUp($args[2]);
				$actor->getRoom()->save();
				return Server::out($actor, "Up changed");
			}
			
			if($args[1] == 'down')
			{
				$actor->getRoom()->setDown($args[2]);
				$actor->getRoom()->save();
				return Server::out($actor, "Down changed");
			}
			
			if($args[1] == 'area')
			{
				$actor->getRoom()->setArea($args[2]);
				$actor->getRoom()->save();
				Return Server::out($actor, "Area changed");
			}
			
			return Server::out($actor, "What was that?");
			
		}
	}
?>
