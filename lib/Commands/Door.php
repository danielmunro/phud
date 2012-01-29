<?php
namespace Commands;
use \Mechanics\Alias;
use \Mechanics\Server;
use \Mechanics\Door as mDoor;
use \Mechanics\Room as mRoom;
use \Mechanics\Command\DM;
use \Living\User;

class Door extends DM
{

	protected function __construct()
	{
		self::addAlias('door', $this);
	}

	public function perform(User $user, $args = array())
	{
		if(sizeof($args) <= 1)
			return Server::out($user, "What were you trying to do?");
	
		$command = $this->getCommand($args[1]);
		if($command)
		{
			$fn = 'do'.ucfirst($command);
			$this->$fn($user, $args);
		}
	}
	
	private function doInformation(User $user, $args)
	{
	}
	
	private function doCreate(User $user, $args)
	{
		if(!$this->hasArgCount($user, $args, 3))
			return;
		
		$direction = mRoom::getDirectionStr($args[2]);
		if(!$direction)
			return Server::out($user, "That direction doesn't exist.");
		
		$door1 = new mDoor();
		
		$fn = 'get'.ucfirst($direction);
		$dir_id = $user->getRoom()->$fn();
		$room = mRoom::find($dir_id);
		$rev_dir = mRoom::getReverseDirection($direction);
		$door2 = new mDoor();
		$door1->setPartnerDoor($door2);
		$door2->setPartnerDoor($door1);
		$user->getRoom()->setDoor($direction, $door1);
		$room->setDoor($rev_dir, $door2);
		
		Server::out($user, "You have created ".$door1." to the ".$direction." direction, and ".$door2." to the ".$rev_dir." direction.");
	}
	
	private function getCommand($arg)
	{
		$commands = array('information', 'create');
		
		$command = array_filter($commands, function($c) use ($arg) 
			{
				return strpos($c, $arg) === 0;
			});
		
		if(sizeof($command))
			return str_replace(' ', '', array_shift($command));
		
		return false;
	}
}
?>
