<?php
namespace Phud\Commands\Arguments;
use Phud\Items\Container as iContainer;

class Container extends Item
{
	public function parse($arg)
	{
		foreach($this->search_in->getManyUsablesByInput($this->search_in->getItems(), $arg) as $item) {
			if($item instanceof iContainer) {
				return $item;
			}
		}
		$this->failItem();
	}
}
