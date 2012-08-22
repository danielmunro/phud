<?php
namespace Phud\Commands\Arguments;
use Phud\Items\Food as iFood;

class Food extends Item
{
	public function parse($arg)
	{
		foreach($this->search_in->getManyUsablesByInput($this->search_in->getItems(), $arg) as $item) {
			if($item instanceof iFood) {
				return $item;
			}
		}
		$this->failItem();
	}
}
