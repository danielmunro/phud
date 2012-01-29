<?php
namespace Commands;
use \Mechanics\Alias,
	\Mechanics\Actor,
	\Mechanics\Server,
	\Mechanics\Command\Command,
	\Mechanics\Item as mItem;

class Drop extends Command
{

	protected $dispositions = array(Actor::DISPOSITION_STANDING, Actor::DISPOSITION_SITTING);

	protected function __construct()
	{
		self::addAlias('drop', $this);
	}

	public function perform(Actor $actor, $args = array())
	{
		$item = implode(' ', array_slice($args, 1, sizeof($args)-1));
		$item = $actor->getItemByInput($item);
		
		if(!($item instanceof mItem)) {
			return Server::out($actor, "You do not have anything like that.");
		}
		
		$item->transferOwnership($actor, $actor->getRoom());

		Server::out($actor, "You drop ".$item.".");
	}
}
?>
