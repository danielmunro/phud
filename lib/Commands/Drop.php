<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Server,
	Phud\Items\Item;

class Drop extends Command
{
	protected $alias = 'drop';
	protected $dispositions = [Actor::DISPOSITION_STANDING, Actor::DISPOSITION_SITTING];

	public function perform(Actor $actor, $args = array())
	{
		$item = implode(' ', array_slice($args, 1, sizeof($args)-1));
		$item = $actor->getItemByInput($item);
		
		if(!($item instanceof Item)) {
			return Server::out($actor, "You do not have anything like that.");
		}
		
		$item->transferOwnership($actor, $actor->getRoom());

		Server::out($actor, "You drop ".$item.".");
	}
}
?>
