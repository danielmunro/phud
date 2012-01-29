<?php
namespace Commands;
use \Mechanics\Alias,
	\Mechanics\Actor,
	\Mechanics\Command\Command,
	\Items\Container,
	\Items\Item as iItem;

class Put extends Command
{
	protected $alias = 'put';
	protected $dispositions = [
		Actor::DISPOSITION_STANDING,
		Actor::DISPOSITION_SITTING
	];

	public function perform(Actor $actor, $args = array())
	{
		
		$item = $actor->getItemByInput($args);
		
		if(!($item instanceof iItem))
			return Server::out($actor, "You don't appear to have that.");
		
		array_shift($args);
		
		$target = $actor->getContainerByInput($args);
		if(!($target instanceof Container))
			$target = $actor->getRoom()->getContainerByInput($args);
		if(!($target instanceof Container))
			return Server::out($actor, "You don't have anything to put that in.");
		
		$item->transferOwnership($actor, $target);
		
		Server::out($actor, "You put ".$item." in ".$target.".");
	}
}
?>
