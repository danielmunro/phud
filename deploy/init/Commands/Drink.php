<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Items\Item as mItem,
	Phud\Items\Drink as iDrink;

class Drink extends User
{
	protected $alias = 'drink';
	protected $dispositions = [
		Actor::DISPOSITION_STANDING,
		Actor::DISPOSITION_SITTING
	];
	
	public function perform(Actor $actor, $args = [])
	{
		$drinkable = implode(' ', array_slice($args, 1));
		$item = null;
		if($drinkable) {
			$item = $actor->getItemByInput($drinkable);
			if(!$item) {
				$item = $actor->getRoom()->getItemByInput($drinkable);
			}
		}
		if(!$item) {
			$items = array_merge($actor->getItems(), $actor->getRoom()->getItems());
			foreach($items as $i) {
				if($i instanceof iDrink) {
					$item = $i;
					break;
				}
			}
		}
		
		$out = '';

		if(!($item instanceof mItem)) {
			$out = "Nothing like that is here.";
		} else if(!($item instanceof iDrink)) {
			$out = "You can't drink that!";
		} else if($item->drink($actor)) {
			$out = "You drink ".$item->getContents()." from ".$item.".";
		} else {
			$out = "There's no ".$item->getContents()." left.";
		}

		$actor->notify($out);
	}
}
