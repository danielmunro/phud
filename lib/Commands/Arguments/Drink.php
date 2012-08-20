<?php
namespace Phud\Commands\Arguments;
use Phud\Actors\Actor as aActor,
	Phud\Items\Drink as iDrink;

class Drink extends Item
{
	protected function parseArg(aActor $actor, $arg = null)
	{
		if($arg === null) {
			$drink = $this->checkInv($actor);
			if(!$drink) {
				$drink = $this->checkInv($actor->getRoom());
			}
			if(!$drink) {
				$this->fail($actor, "Nothing is there to drink.");
			}
			return $drink;
		}
		foreach($this->search_in->getManyUsablesByInput($this->search_in->getItems(), $arg) as $item) {
			if($item instanceof iDrink) {
				return $item;
			}
		}
		$this->failItem($actor);
	}

	private function checkInv($thingWithInventory)
	{
		foreach($thingWithInventory->getItems() as $item) {
			if($item instanceof iDrink) {
				return $item;
			}
		}
	}
}
