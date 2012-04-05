<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Server,
	Phud\Items\Container,
	Phud\Items\Item as mItem;

class Put extends Command
{
	protected $alias = 'put';
	protected $dispositions = [
		Actor::DISPOSITION_STANDING,
		Actor::DISPOSITION_SITTING
	];

	public function perform(Actor $actor, $args = [])
	{
		$s = sizeof($args);
		$item = $actor->getItemByInput(implode(' ', array_slice($args, 1, $s-2)));
		
		if(!($item instanceof mItem)) {
			return Server::out($actor, "You don't appear to have that.");
		}
		
		$target = $actor->getContainerByInput($args[$s-1]);
		if(!($target instanceof Container)) {
			$target = $actor->getRoom()->getContainerByInput($args[$s-1]);
		}
		if(!($target instanceof Container)) {
			return Server::out($actor, "You don't have anything to put that in.");
		}
		
		$item->transferOwnership($actor, $target);
		
		Server::out($actor, "You put ".$item." in ".$target.".");
	}
}
?>
