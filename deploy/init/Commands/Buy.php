<?php
namespace Phud\Commands;
use Phud\Items\Item as iItem,
	Phud\Actors\Actor,
	Phud\Actors\Shopkeeper as aShopkeeper;

class Buy extends Command
{
	protected $alias = 'buy';
	protected $min_argument_count = 1;
	protected $min_argument_fail = "Buy what?";
	protected $dispositions = [
		Actor::DISPOSITION_STANDING,
		Actor::DISPOSITION_SITTING
	];

	public function perform(Actor $buyer, iItem $item, aShopkeeper $shopkeeper)
	{
		if($buyer->decreaseFunds($item->getValue()) === false) {
			return Say::perform($shopkeeper, $shopkeeper->getNotEnoughMoneyMessage());
		}

		$shopkeeper->modifyCurrency('copper', $item->getValue());
		
		$new_item = clone $item;
		$buyer->addItem($new_item);
		return $buyer->notify("You buy ".$item." from ".$shopkeeper." for ".$item->getValue()." copper.");
	}

	protected function getArgumentsFromHints(Actor $buyer, $args)
	{
		$shopkeeper = (new Arguments\Shopkeeper())->parse($buyer, sizeof($args) === 3 ? $args[2] : null);
		return [
			(new Arguments\Item($shopkeeper))->parse($buyer, $args[1]),
			$shopkeeper
		];
	}
}
