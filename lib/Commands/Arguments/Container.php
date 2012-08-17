<?php
namespace Phud\Commands\Arguments;
use Phud\Actors\Actor as aActor,
	Phud\Items\Container as iContainer;

class Container extends Item
{
	protected function parseArg(aActor $actor, $arg)
	{
		foreach($this->search_in->getManyUsablesByInput($this->search_in->getItems(), $arg) as $item) {
			if($item instanceof iContainer) {
				return $item;
			}
		}
		$this->failItem($actor);
	}
}
