<?php
namespace Phud\Commands\Arguments;
use Phud\Items\Drink as iDrink;

class Drink extends Item
{
	public function parse($arg = null)
	{
		if($arg === null) {
			$drink = $this->checkInv($this->search_in);
			if(!$drink) {
				$drink = $this->checkInv($this->search_in->getRoom());
			}
			if(!$drink) {
				$this->fail("Nothing is there to drink.");
			}
			return $drink;
		}
		foreach($this->search_in->getManyUsablesByInput($this->search_in->getItems(), $arg) as $item) {
			if($item instanceof iDrink) {
				return $item;
			}
		}
		$this->failItem();
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
