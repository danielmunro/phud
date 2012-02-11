<?php
namespace Commands;
use \Items\Item as mItem,
	\Mechanics\Alias,
	\Mechanics\Actor,
	\Mechanics\Server,
	\Mechanics\Ability,
	\Mechanics\Command\Command,
	\Living\Shopkeeper as lShopkeeper;

class Buy extends Command
{
	protected $alias = 'buy';
	protected $dispositions = [
		Actor::DISPOSITION_STANDING,
		Actor::DISPOSITION_SITTING
	];

	public function perform(Actor $actor, $args = [])
	{
	
		if(sizeof($args) == 3)
			$target = $actor->getRoom()->getActorByInput();
		else
		{
			$targets = $actor->getRoom()->getActors();
			foreach($targets as $potential_target)
				if($potential_target instanceof lShopkeeper)
					$target = $potential_target;
		}
		
		if(!($target instanceof Actor))
			return Server::out($actor, "They are not here.");
		
		if(!($target instanceof lShopkeeper))
			return Server::out($actor, $target->getAlias(true) . " is not a shop keeper.");
		
		$item = $target->getItemByInput($args[1]);
		
		if(!($item instanceof mItem))
			return Say::perform($target, $target->getNoItemMessage());
		
		$value = 1;

		if($actor->decreaseFunds($value) === false)
			return Say::perform($target, $target->getNotEnoughMoneyMessage());
		
		$new_item = clone $item;
		$actor->addItem($new_item);
		return Server::out($actor, "You buy " . $item->getShort() . " for " . $item->getValue() . " copper.");
	}
}
?>
