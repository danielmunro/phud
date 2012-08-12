<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Items\Item,
	Phud\Items\Food;

class Eat extends User
{
	protected $alias = 'eat';
	protected $dispositions = [
		Actor::DISPOSITION_STANDING,
		Actor::DISPOSITION_SITTING
	];

	public function perform(Actor $actor, $args = [])
	{
		
		$item = $actor->getItemByInput(implode(' ', array_slice($args, 1)));
		
		if(!($item instanceof Item)) {
			return $actor->notify("Nothing like that is here.");
		}
		
		if(!($item instanceof Food)) {
			return $actor->notify("You can't eat that!");
		}
		
		$actor->notify("You eat ".$item.".");
		$actor->consume($item);
	}
}
