<?php
namespace Phud\Commands;
use Phud\Server,
	Phud\Actors\Actor,
	Phud\Items\Item,
	Phud\Items\Food;

class Eat extends User
{
	protected $alias = 'eat';
	protected $dispositions = [
		Actor::DISPOSITION_STANDING,
		Actor::DISPOSITION_SITTING
	];

	public function perform(Actor $actor, $args = array())
	{
		
		$item = $actor->getItemByInput(implode(' ', array_slice($args, 1)));
		
		if(!($item instanceof mItem))
			return Server::out($actor, "Nothing like that is here.");
		
		if(!($item instanceof Food))
			return Server::out($actor, "You can't eat that!");
		
		Server::out($actor, "You eat ".$item.".");
		$actor->consume($item);
	}
}
?>
