<?php
namespace Commands;
use \Mechanics\Alias;
use \Mechanics\Server;
use \Mechanics\Room as mRoom;
use \Mechanics\Command\DM;
use \Living\User as lUser;

class Room extends DM
{
	protected $alias = 'room';
	
	public function perform(lUser $user, $args = array())
	{
		if($args[1] == 'new' || $args[1] == 'create')
		{
		
			$direction = $this->isValidDirection($args[2]);
			if(!$direction)
				return Server::out($user, "That direction doesn't exist.");
		
			$room = new mRoom();
			$user->getRoom()->{'set' . ucfirst($direction)}($room->getId());
			$new_direction = mRoom::getReverseDirection($direction);
			$room->{'set' . ucfirst($new_direction)}($user->getRoom()->getId());
			
			return Server::out($user, "You've created a new room to the " . $direction . ".");
		}
		
		if($args[1] == 'id')
			return Server::out($user, "ID: " . $user->getRoom()->getId());
		
		$property = $this->isValidProperty($args[1]);
		if($property)
		{
			if(is_numeric($property[0])) {
				$fn = 'set' . ucfirst($property[1]);
			} else {
				$fn = $property[0];
			}
			array_shift($args);
			array_shift($args);
			$value = implode(' ', $args);
			$user->getRoom()->$fn($value);
			return Server::out($user, 'Property set.');
		}
		
		if($args[1] == 'copy')
		{
		
			$direction = $this->isValidDirection($args[2]);
			if(!$direction)
				return Server::out($user, "That direction doesn't exist.");
		
			$room = new mRoom();
			$user->getRoom()->{'set' . ucfirst($direction)}($room->getId());
			$new_direction = mRoom::getReverseDirection($direction);
			$room->setTitle($user->getRoom()->getTitle());
			$room->setDescription($user->getRoom()->getDescription());
			$room->setArea($user->getRoom()->getArea());
			$room->{'set' . ucfirst($new_direction)}($user->getRoom()->getId());
			return Server::out($user, 'Property set.');
		}
		
		if(strpos('information', $args[1]) === 0)
		{
			return Server::out($user, 
							"Information on room (#".$user->getRoom()->getId()."):\n".
							"title:                  ".$user->getRoom()->getTitle()."\n".
							"area:                   ".$user->getRoom()->getArea()."\n".
							"movement cost:          ".$user->getRoom()->getMovementCost()."\n".
							"description:\n".$user->getRoom()->getDescription());
		}
		
		return Server::out($user, "What was that?");
		
	}
	
	private function isValidProperty($property)
	{
		$dirs = array('title', 'description', 'area', 'north', 'south', 'east', 'west', 'up', 'down', 'setMovementCost' => 'movement_cost');
	
		foreach($dirs as $k => $p)
			if(strpos($p, $property) === 0)
				return [$k, $p];
		
		return false;
	
	}
	
	private function isValidDirection($dir)
	{
		$dirs = array('north', 'south', 'east', 'west', 'up', 'down');
	
		foreach($dirs as $d)
			if(strpos($d, $dir) === 0)
				return $d;
		
		return false;
	}
}
?>
