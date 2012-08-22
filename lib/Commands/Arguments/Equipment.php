<?php
namespace Phud\Commands\Arguments;
use Phud\Items\Equipment as iEquipment;

class Equipment extends Item
{
	public function parse($arg)
	{
		foreach($this->search_in->getManyUsablesByInput($this->search_in->getItems(), $arg) as $item) {
			if($item instanceof iEquipment) {
				return $item;
			}
		}
		$this->failItem();
	}
}
