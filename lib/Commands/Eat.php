<?php
namespace Commands;
use \Mechanics\Actor,
	\Mechanics\Alias,
	\Mechanics\Server,
	\Mechanics\Command\Command,
	\Mechanics\Item as mItem,
	\Items\Food;

class Eat extends Command
{
	protected $dispositions = [
		Actor::DISPOSITION_STANDING,
		Actor::DISPOSITION_SITTING
	];

	protected function __construct()
	{
		self::addAlias('eat', $this);
	}

	public function perform(Actor $actor, $args = array())
	{
		
		$item = $actor->getItemByInput(implode(' ', array_slice($args, 1)));
		
		if(!($item instanceof mItem))
			return Server::out($actor, "Nothing like that is here.");
		
		if(!($item instanceof Food))
			return Server::out($actor, "You can't eat that!");
		
		if($actor->increaseHunger($item->getNourishment())) {
			$actor->removeItem($item);
			Server::out($actor, "You eat " . $item->getShort() . ".");
		}
	}
}
?>
